<?php

namespace App\Services\Mango;

use App\Events\ClearNotify;
use App\Events\MangoIncome;
use App\Events\OrderCreated;
use App\Events\OrderProcessed;
use App\Helpers\ShowroomHelper;
use App\Jobs\ProcessCall;
use App\Jobs\ProcessRecord;
use App\Models\ActivityLog;
use App\Models\MangoCall;
use App\Models\MangoCallRecording;
use App\Models\MissedCall;
use App\Models\Order;
use App\Models\PhoneCode;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Contracts\Activity;
use Throwable;

class MangoCallService
{
    protected MangoCallData $callData;
    protected MangoSiteResolver $sites;

    public function __construct(MangoCallData $callData, MangoSiteResolver $sites)
    {
        $this->callData = $callData;
        $this->sites = $sites;
    }

    public function handleRealtime(object $payload, int $accountId): void
    {
        $entryId = trim((string) ($payload->entry_id ?? ''));
        $callId = trim((string) ($payload->call_id ?? ''));
        if ($entryId === '' || $callId === '') {
            Log::warning('Mango call ignored: entry_id or call_id is missing', [
                'account_id' => $accountId,
            ]);
            return;
        }

        $lock = Cache::lock("mango:call:{$accountId}:" . sha1($entryId), 10);
        try {
            $lock->block(3, function () use ($payload, $accountId, $entryId, $callId) {
                $direction = $this->callData->directionFromRealtime($payload);
                $showroomId = $this->resolveShowroomId($payload, $accountId);
                $call = MangoCall::firstOrCreate(
                    ['mango_account_id' => $accountId, 'entry_id' => $entryId],
                    ['showroom_id' => $showroomId, 'status' => 'new']
                );

                $seq = (int) ($payload->seq ?? 0);
                $sequences = $call->call_sequences ?: [];
                if (array_key_exists($callId, $sequences) && $seq <= (int) $sequences[$callId]) {
                    return;
                }
                $sequences[$callId] = $seq;

                $call->call_sequences = $sequences;
                $call->payload = $this->payloadToArray($payload);
                $call->showroom_id = $call->showroom_id ?: $showroomId;
                if ($direction !== null) {
                    $call->direction = $direction;
                }

                if ($direction === MangoCallData::INCOMING) {
                    $this->handleIncomingRealtime($call, $payload, $accountId);
                } elseif ($direction === MangoCallData::OUTGOING) {
                    $call->client_phone = $this->callData->clientPhone($payload, $direction);
                    $call->operator_extension = $this->callData->operatorExtension($payload, $direction);
                    $call->status = strtolower((string) ($payload->call_state ?? 'new'));
                } elseif ($direction === MangoCallData::INTERNAL) {
                    $call->status = strtolower((string) ($payload->call_state ?? 'new'));
                }

                $call->save();
            });
        } catch (Throwable $e) {
            Log::error('Mango realtime processing failed', [
                'account_id' => $accountId,
                'entry_id' => $entryId,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function handleSummary(object $payload, int $accountId): void
    {
        $entryId = trim((string) ($payload->entry_id ?? ''));
        $direction = (int) ($payload->call_direction ?? -1);
        if ($entryId === '' || !in_array($direction, [0, 1, 2], true)) {
            Log::warning('Mango summary ignored: invalid identity or direction', [
                'account_id' => $accountId,
                'entry_id' => $entryId,
                'direction' => $payload->call_direction ?? null,
            ]);
            return;
        }

        $lock = Cache::lock("mango:summary:{$accountId}:" . sha1($entryId), 15);
        try {
            $lock->block(5, function () use ($payload, $accountId, $entryId, $direction) {
                DB::transaction(function () use ($payload, $accountId, $entryId, $direction) {
                    $showroomId = $this->resolveShowroomId($payload, $accountId);
                    $call = MangoCall::where('mango_account_id', $accountId)
                        ->where('entry_id', $entryId)
                        ->lockForUpdate()
                        ->first();

                    if (!$call) {
                        $call = new MangoCall([
                            'mango_account_id' => $accountId,
                            'entry_id' => $entryId,
                            'showroom_id' => $showroomId,
                        ]);
                    }
                    if ($call->summary_processed) {
                        return;
                    }

                    $showroomId = (int) ($call->showroom_id ?: $showroomId);
                    $phone = $this->callData->clientPhone($payload, $direction)
                        ?: $call->client_phone;
                    $lineNumber = $this->callData->lineNumber($payload, true)
                        ?: $call->line_number;
                    $site = $this->sites->resolve($lineNumber, [$accountId, $showroomId]);
                    if (!$site && $call->site_id) {
                        $site = Site::find($call->site_id);
                    }
                    $extension = $this->callData->operatorExtension($payload, $direction);
                    $operator = $this->resolveOperator($extension, $accountId, $showroomId);
                    $order = $call->order_id ? Order::find($call->order_id) : null;

                    if (!$order && $phone) {
                        $order = $this->findOrder($phone, $showroomId);
                    }
                    if ($direction === MangoCallData::INCOMING && !$order && $phone) {
                        $order = $this->createIncomingOrder(
                            $phone,
                            $showroomId,
                            $site,
                            $lineNumber,
                            (int) ($payload->create_time ?? time())
                        );
                    }

                    $talkTime = (int) ($payload->talk_time ?? 0);
                    $endTime = (int) ($payload->end_time ?? 0);
                    $isAnsweredIncoming = $direction === MangoCallData::INCOMING && $talkTime > 0;
                    $isMissedIncoming = $direction === MangoCallData::INCOMING && $talkTime === 0;
                    $historyType = $direction === MangoCallData::OUTGOING
                        ? 6
                        : ($isMissedIncoming ? 7 : 5);

                    if ($order) {
                        if ($isAnsweredIncoming && $operator) {
                            $order->operator_id = $operator->id;
                            $order->source_id = 25;
                            $order->save();
                            DB::afterCommit(function () use ($order) {
                                OrderProcessed::dispatch($order);
                            });
                        } elseif ($isMissedIncoming) {
                            if ($call->order_id === $order->id && $order->client_name === 'Новый клиент') {
                                $order->client_name = 'Пропущенный звонок';
                                $order->save();
                            }
                            $missed = new MissedCall();
                            $missed->order_id = $order->id;
                            $missed->save();
                        }

                        $this->upsertHistory($entryId, $historyType, $order, $operator, [
                            'direction' => $direction,
                            'create_time' => (int) ($payload->create_time ?? 0),
                            'forward_time' => (int) ($payload->forward_time ?? 0),
                            'talk_time' => $talkTime,
                            'end_time' => $endTime,
                            'duration' => $talkTime > 0 && $endTime >= $talkTime
                                ? $endTime - $talkTime
                                : 0,
                            'line_number' => $lineNumber,
                            'site_id' => $site->id ?? null,
                            'ext' => $extension,
                            'entry_result' => isset($payload->entry_result)
                                ? (int) $payload->entry_result
                                : null,
                            'disconnect_reason' => isset($payload->disconnect_reason)
                                ? (int) $payload->disconnect_reason
                                : null,
                        ]);

                        $operatorId = $operator->id ?? null;
                        DB::afterCommit(function () use ($order, $operatorId, $historyType) {
                            ProcessCall::dispatch($order, $operatorId, $historyType);
                        });
                    }

                    $call->fill([
                        'showroom_id' => $showroomId,
                        'order_id' => $order->id ?? null,
                        'site_id' => $site->id ?? null,
                        'operator_id' => $operator->id ?? null,
                        'direction' => $direction,
                        'client_phone' => $phone,
                        'line_number' => $lineNumber,
                        'operator_extension' => $extension,
                        'status' => $isMissedIncoming ? 'missed' : 'completed',
                        'entry_result' => isset($payload->entry_result)
                            ? (int) $payload->entry_result
                            : null,
                        'disconnect_reason' => isset($payload->disconnect_reason)
                            ? (int) $payload->disconnect_reason
                            : null,
                        'create_time' => (int) ($payload->create_time ?? 0),
                        'talk_time' => $talkTime,
                        'end_time' => $endTime,
                        'summary_processed' => true,
                        'payload' => $this->payloadToArray($payload),
                    ]);
                    $call->save();

                    if ($direction === MangoCallData::INCOMING) {
                        DB::afterCommit(function () use ($showroomId, $entryId) {
                            ClearNotify::dispatch($showroomId, $entryId);
                        });
                    }
                });
            });
        } catch (Throwable $e) {
            Log::error('Mango summary processing failed', [
                'account_id' => $accountId,
                'entry_id' => $entryId,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function handleRecording(object $payload, int $accountId): void
    {
        $entryId = trim((string) ($payload->entry_id ?? ''));
        $recordingId = trim((string) ($payload->recording_id ?? ''));
        if ($entryId === '' || $recordingId === '') {
            return;
        }

        $call = MangoCall::where('mango_account_id', $accountId)
            ->where('entry_id', $entryId)
            ->first();
        $recording = MangoCallRecording::firstOrCreate(
            ['recording_id' => $recordingId],
            [
                'mango_call_id' => $call->id ?? null,
                'mango_account_id' => $accountId,
                'entry_id' => $entryId,
                'user_id' => $payload->user_id ?? null,
                'recorded_at' => $payload->timestamp ?? null,
                'payload' => $this->payloadToArray($payload),
            ]
        );

        if ($recording->wasRecentlyCreated || !$recording->attached_at) {
            ProcessRecord::dispatch($entryId, $recordingId)->delay(now()->addMinute());
        }
    }

    protected function handleIncomingRealtime(
        MangoCall $call,
        object $payload,
        int $accountId
    ): void {
        $phone = $this->callData->clientPhone($payload, MangoCallData::INCOMING);
        if (!$phone) {
            return;
        }

        $showroomId = (int) $call->showroom_id;
        $lineNumber = $this->callData->lineNumber($payload);
        $site = $this->sites->resolve($lineNumber, [$accountId, $showroomId]);
        $extension = $this->callData->operatorExtension($payload, MangoCallData::INCOMING);
        $state = (string) ($payload->call_state ?? '');

        $call->client_phone = $phone;
        $call->line_number = $lineNumber;
        $call->site_id = $site->id ?? $call->site_id;
        $call->operator_extension = $extension ?: $call->operator_extension;
        $call->status = strtolower($state ?: 'new');

        $order = $call->order_id ? Order::find($call->order_id) : null;
        if (!$order) {
            $order = $this->findOrder($phone, $showroomId);
        }

        if ($state === 'Appeared' && !$order) {
            $order = $this->createIncomingOrder(
                $phone,
                $showroomId,
                $site,
                $lineNumber,
                (int) ($payload->timestamp ?? time())
            );
        }
        if ($order) {
            $call->order_id = $order->id;
        }

        if ($state === 'Appeared' && !$call->popup_sent && $order) {
            $operator = $this->resolveOperator($extension, $accountId, $showroomId);
            MangoIncome::dispatch(
                $this->popupPayload($call, $payload, $order, $site, $operator),
                $showroomId
            );
            $call->popup_sent = true;
        }

        if ($state === 'Connected' && ($payload->location ?? null) === 'abonent' && $order) {
            $operator = $this->resolveOperator($extension, $accountId, $showroomId);
            if ($operator) {
                $order->operator_id = $operator->id;
                $order->source_id = 25;
                $order->save();
                $call->operator_id = $operator->id;
                OrderProcessed::dispatch($order);
            }
            ClearNotify::dispatch($showroomId, $call->entry_id);
        }
    }

    protected function createIncomingOrder(
        string $phone,
        int $showroomId,
        ?Site $site,
        ?string $lineNumber,
        int $timestamp
    ): Order {
        $info = $this->phoneInfo($phone);
        $order = new Order();
        $order->client_name = 'Новый клиент';
        $order->showroom_id = $showroomId;
        $order->status_id = 1;
        $order->phone = $phone;
        $order->site_id = $site->id ?? null;
        $order->source_id = 20;
        $order->line_number = $site && $this->isSip($lineNumber)
            ? $site->phone
            : $lineNumber;
        if (now()->between(
            now()->copy()->setTime(8, 0),
            now()->copy()->setTime(20, 0)
        )) {
            $order->operator_id = 1000;
        }
        $regionName = $info->region->name ?? null;
        $order->comment = trim(($regionName ? $regionName . ' ' : '')
            . Carbon::createFromTimestamp($timestamp)->format('d.m.Y H:i:s'));
        $order->save();
        if (DB::transactionLevel() > 0) {
            DB::afterCommit(function () use ($order) {
                OrderCreated::dispatch($order);
            });
        } else {
            OrderCreated::dispatch($order);
        }

        return $order->fresh(['operator', 'region', 'status']);
    }

    protected function findOrder(string $phone, int $showroomId): ?Order
    {
        $variants = [$phone, '+' . $phone, '8' . substr($phone, 1)];
        $fields = ['phone', 'phone_2', 'phone_3', 'work_phone'];

        return Order::query()
            ->where('showroom_id', $showroomId)
            ->where(function ($query) use ($fields, $variants) {
                foreach ($fields as $field) {
                    $query->orWhereIn($field, $variants);
                }
            })
            ->with(['operator', 'region', 'status'])
            ->latest('updated_at')
            ->latest('id')
            ->first();
    }

    protected function resolveOperator(
        ?string $extension,
        int $accountId,
        int $showroomId
    ): ?User {
        if (!$extension) {
            return null;
        }

        $showrooms = array_values(array_unique([$accountId, $showroomId]));
        $day = strtolower(Carbon::now()->format('l'));
        $scheduled = User::where('work_place', $extension)
            ->whereIn('showroom_id', $showrooms)
            ->whereHas('operatorSchedule', function ($query) use ($day) {
                $query->where($day, '1');
            })
            ->latest('updated_at')
            ->first();

        return $scheduled ?: User::where('work_place', $extension)
            ->whereIn('showroom_id', $showrooms)
            ->latest('updated_at')
            ->first();
    }

    protected function phoneInfo(string $phone): ?PhoneCode
    {
        return PhoneCode::with(['region', 'operator'])
            ->where('abc_def', substr($phone, 1, 3))
            ->where('from', '<=', substr($phone, -7))
            ->where('to', '>=', substr($phone, -7))
            ->first();
    }

    protected function popupPayload(
        MangoCall $call,
        object $payload,
        Order $order,
        ?Site $site,
        ?User $callOperator
    ): array {
        $order->loadMissing(['operator', 'region', 'status']);
        $info = $this->phoneInfo($call->client_phone);

        return [
            'entry_id' => $call->entry_id,
            'direction' => 'inbound',
            'call_state' => 'ringing',
            'phone' => $call->client_phone,
            'client_name' => $order->client_name,
            'status' => $order->status,
            'site_id' => $site->id ?? null,
            'site_name' => $site->title ?? null,
            'line_number' => $site && $this->isSip($call->line_number)
                ? $site->phone
                : $call->line_number,
            'assigned_operator' => $order->operator,
            'call_operator' => $callOperator,
            'client_region' => $order->region,
            'phone_region' => $info->region ?? null,
            'phone_operator' => $info->operator ?? null,
            'order_id' => $order->id,
            'showroom_id' => $call->showroom_id,
            'order_created_at' => $order->created_at,
            'call_started_at' => Carbon::createFromTimestamp(
                (int) ($payload->timestamp ?? time())
            )->toIso8601String(),
        ];
    }

    protected function upsertHistory(
        string $entryId,
        int $type,
        Order $order,
        ?User $operator,
        array $properties
    ): void {
        $history = ActivityLog::where('entry_id', $entryId)
            ->whereIn('description', ['5', '6', '7'])
            ->first();

        if ($history) {
            $history->description = (string) $type;
            $history->subject_type = 2;
            $history->subject_id = $order->id;
            $history->causer_id = $operator->id ?? null;
            $history->properties = $properties;
            $history->save();
            return;
        }

        activity()
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($type, $order, $entryId, $operator) {
                $activity->subject_type = 2;
                $activity->subject_id = $order->id;
                $activity->causer_type = null;
                $activity->causer_id = $operator->id ?? null;
                $activity->entry_id = $entryId;
            })
            ->log($type);
    }

    protected function resolveShowroomId(object $payload, int $accountId): int
    {
        $group = $payload->to->acd_group ?? null;
        if (in_array((int) $group, [300, 400, 776], true)) {
            return (int) (ShowroomHelper::getShowroomPair($accountId) ?: $accountId);
        }

        return $accountId;
    }

    protected function isSip(?string $value): bool
    {
        $value = (string) $value;

        return stripos($value, 'sip:') !== false
            || strpos($value, '@') !== false
            || stripos($value, 'mangosip.ru') !== false;
    }

    protected function payloadToArray(object $payload): array
    {
        return json_decode(json_encode($payload), true) ?: [];
    }
}
