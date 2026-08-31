<?php

namespace App\Http\Controllers;

use App\Events\ClearNotify;
use App\Events\MangoIncome;
use App\Events\OrderCreated;
use App\Events\OrderProcessed;
use App\Helpers\GeneralHelper;
use App\Helpers\ShowroomHelper;
use App\Jobs\ProcessCall;
use App\Jobs\ProcessRecord;
use App\Models\MissedCall;
use App\Models\Order;
use App\Models\PhoneCode;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sharoff\Mango\Api\MangoHelper;
use Spatie\Activitylog\Contracts\Activity;
use Throwable;


class MangoEventController extends Controller
{

    public function __construct()
    {
        $this->dayName = Carbon::now()->format('l');
    }

    protected function mangoLog(string $level, string $message, array $context = []): void
    {
        try {
            // On-demand channel: does not depend on config:cache / logging.channels.mango
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/mango.log'),
                'level' => 'debug',
            ])->{$level}($message, $context);
        } catch (Throwable $e) {
            @file_put_contents(
                storage_path('logs/mango.log'),
                date('Y-m-d H:i:s') . " {$level}: {$message} " . json_encode($context, JSON_UNESCAPED_UNICODE) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
        }
    }


    public function summary(Request $request, $id)
    {
        try {
            MangoHelper::setApiKey(config('mango.api_key_' . $id))->setApiSalt(config('mango.api_salt_' . $id));
            $response = MangoHelper::getMethodData();
            $this->mangoLog('info', 'summary', [
                'showroom_id' => $id,
                'response' => $response,
            ]);


        $groups = [300, 400, 776];

        if (isset($response->to->acd_group) && in_array($response->to->acd_group, $groups)) {
            $showroom_id = ShowroomHelper::getShowroomPair($id);
            ///Log::emergency(['showroom' => $id, 'resp' => $response]);
        } else {
            $showroom_id = $id;
        }


        //$test = new Test();
        //$test->name = "summary";
        //$test->body = json_encode($response);
        //$test->save();
        $extension = null;
        $phone = null;
        if (($response->call_direction ?? null) === 1) {
            $phone = $this->normalizeMangoPhone($response->from->number ?? null);
            if (isset($response->from->extension)) {
                $extension = $response->from->extension;
            }
        } else {
            $phone = $this->normalizeMangoPhone($response->to->number ?? null);
            if (isset($response->to->extension)) {
                $extension = $response->to->extension;
            }


        }

        if ($extension !== null) {
            $user = User::where('work_place', $extension)->where('showroom_id', $id)->whereHas('operatorSchedule', function ($query) {
                $query->where(strtolower($this->dayName), "1");
            })->latest()->first();

            if (empty($user)) {
                Log::notice("Unknown operator:" . $extension);
                $user = User::where('work_place', $extension)->where('showroom_id', $id)->latest('updated_at')->first();
            }
        }

        if (isset($response->line_number)) {
            try {
                $site_phone = $this->findSiteByLineNumber($response->line_number);
            } catch (Throwable $e) {
                Log::error($e);
                Log::info('resp:', [$response]);
            }
            if (empty($site_phone)) {
                Log::notice("Unknown phone(summary - " . $showroom_id . "):" . @$response->line_number);
            }
        }


        if (!$phone) {
            $this->mangoLog('info', 'summary skip, bad phone', [
                'showroom_id' => $showroom_id,
                'from' => $response->from->number ?? null,
                'to' => $response->to->number ?? null,
            ]);
            return;
        }

        $def = substr($phone, 1, 3);
        $last = substr($phone, -7, 7);
        $info = PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
        $order = Order::searchPhone($phone)->with(['operator', 'region'])->where('showroom_id', $showroom_id)->first();
        $retries = null;

        //outgoing call
        if ($phone && $response->call_direction === 2) {

            if (!empty($order)) {
                $properties = ['create_time' => $response->create_time, 'talk_time' => $response->talk_time, 'end_time' => $response->end_time, 'line_number' => $response->line_number, 'ext' => $response->from->extension ?? null, 'entry_result' => $response->entry_result];
                //$properties['entry_id'] = $response->entry_id;
                ///$this->saveHistory($properties, 6, $order, $response->entry_id);  // 5-income, 6-outgoing, 7-missed, 8-record
                $this->saveCallHistory($properties, 6, $order->id, $response->entry_id, $user->id ?? null);  // 5-income, 6-outgoing, 7-missed, 8-record
                try {
                    ProcessCall::dispatch($order, $user->id ?? null, 6);
                } catch (Throwable $e) {
                    Log::error('ProcessCall outgoing error - ' . $e);
                }
            }

        }

        //income
        if ($phone && $response->call_direction === 1) {

            //if($id === 7) {
            //Log::warning('Test' . $response->to->extension);

            //}


            if (!empty($order)) {
                if ($order->retries > 0) {
                    $retries = $order->retries;
                } else $retries = 1;
            }
            if ($response->entry_result === 1 && !empty($order)) {
                $properties = ['create_time' => $response->create_time, 'talk_time' => $response->talk_time, 'end_time' => $response->end_time, 'line_number' => $response->line_number, 'ext' => $response->to->extension ?? null];

                $this->saveCallHistory($properties, 5, $order->id, $response->entry_id, $user->id ?? null);   // 5-income, 6-outgoing, 7-missed, 8-record
            }

        }


        //missed calls
        if ($phone && $response->call_direction === 1) {
            //Log::critical('entry_result 1 - '.$response->disconnect_reason);
            if (!empty($order) && $response->entry_result === 0 && $response->talk_time === 0) {
                //Log::critical('talktime 1 - '.$response->talk_time);
                $missed = new MissedCall();
                $missed->order_id = $order->id;
                $missed->save();
                // TODO: Save missed calls on activity log
                //$this->saveHistory($properties, 3, $order);
                $properties = ['create_time' => $response->create_time, 'talk_time' => $response->talk_time, 'end_time' => $response->end_time, 'line_number' => $response->line_number, 'disconnect_reason' => $response->disconnect_reason, 'entry_result' => $response->entry_result];
                // TODO: Add properties
                $this->saveCallHistory($properties, 7, $order->id, $response->entry_id, $user->id ?? null);


                try {
                    $order3 = Order::where('operator_id', 1000)->where('showroom_id', $showroom_id)->findPhone($phone)->latest()->first();
                    if (!empty($order3)) {
                        //Log::warning('Dist end-' . $phone.  now()->format('Y-m-d H:i:s'));
                        $order3->operator_id = null;
                        $order3->client_name = "Пропущеный звонок";
                        $order3->save();
                        OrderCreated::dispatch($order3);
                    }
                } catch (Throwable $e) {
                    Log::error($e);
                }

                //missed call new client

                return;
            }
            if ($response->entry_result === 0 && !empty($order) && $response->talk_time === 0) {

                //og::critical('talktime 2 - '.$response->talk_time );
                //$site_phone = Site::where('phone', $response->line_number)->first();
                $order2 = new Order();
                $order2->client_name = "Пропущеный звонок";
                $order2->phone = $phone;
                if (isset($site_phone->id)) {
                    $order2->site_id = $site_phone->id;
                } else {
                    return;
                    $order2->line_number = $response->line_number;
                }
                $order2->showroom_id = $showroom_id ?? null;
                if (isset($order->showroom_id) && $order->showroom_id !== $showroom_id) {
                    $order2->operator_id = $order->operator_id ?? null;
                }
                $order2->status_id = 1;
                $order2->retries = $retries;
                if (isset($info->region)) {
                    $order2->comment = ($info->region !== null ? ($info->region->name . " ") : null) . Carbon::createFromTimestamp($response->create_time);
                }
                $order2->source_id = 1;
                $order2->save();

                $properties = [];

                //$this->saveHistory($properties, 3, $order2);
                //$this->saveHistory($properties, 7, $order2);

                $this->saveCallHistory($properties, 7, $order2->id, $response->entry_id, $user->id ?? null);

                OrderCreated::dispatch($order2);
            }


            ClearNotify::dispatch($showroom_id);
        }
        } catch (Throwable $e) {
            $this->mangoLog('error', 'summary error', [
                'showroom_id' => $id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function test_phone(Request $request)
    {
        $line_number  = Str::after($request->line_number, 'sip:');
        $site_phone = Site::where('sip', 'LIKE', "%".$line_number."%")->first();
        return $site_phone;

    }


    public function call($id)
    {
        //Log::emergency(config('mango.api_key_'.$id));
        //Log::emergency(config('mango.api_salt_'.$id));
        try {

            MangoHelper::setApiKey(config('mango.api_key_' . $id))->setApiSalt(config('mango.api_salt_' . $id));
            $response = MangoHelper::getMethodData();
            $this->mangoLog('info', 'call', [
                'showroom_id' => $id,
                'response' => $response,
            ]);

            $rawPhone = $response->from->number ?? null;
            $phone = $this->normalizeMangoPhone($rawPhone);
            if ($rawPhone && !$phone) {
                $isSip = str_contains((string) $rawPhone, 'sip:')
                    || str_contains((string) $rawPhone, '@')
                    || str_contains((string) $rawPhone, 'mangosip.ru');
                $this->mangoLog('info', $isSip ? 'call skip, sip from' : 'call skip, invalid phone', [
                    'showroom_id' => $id,
                    'raw_phone' => $rawPhone,
                ]);
            }

            $groups = [300, 400, 776];

            if (isset($response->to->acd_group) && in_array($response->to->acd_group, $groups)) {
                $showroom_id = ShowroomHelper::getShowroomPair($id);
            } else {
                $showroom_id = $id;
            }
            $line_number = null;
            $entryId = $response->entry_id ?? null;
            $location = $response->location ?? '';
            // Один звонок даёт несколько Appeared (ivr → queue → abonent), часто все с seq=1.
            $isCallStart = $phone
                && ($response->call_state ?? null) === 'Appeared'
                && (int) ($response->seq ?? 0) === 1
                && in_array($location, ['ivr', 'queue', 'abonent'], true);

            if (($response->call_state ?? null) === 'Appeared' && !$isCallStart) {
                $this->mangoLog('info', 'call skip create', [
                    'showroom_id' => $id,
                    'raw_phone' => $rawPhone,
                    'phone' => $phone,
                    'call_state' => $response->call_state ?? null,
                    'location' => $location,
                    'seq' => $response->seq ?? null,
                    'line_number' => $response->to->line_number ?? null,
                    'entry_id' => $entryId,
                ]);
            }

            $phoneLockKey = 'mango_call_phone_' . $showroom_id . '_' . $phone;
            $gotCallLock = $isCallStart && Cache::add($phoneLockKey, 1, 180);

            if ($isCallStart && !$gotCallLock) {
                $this->mangoLog('info', 'call duplicate skipped', [
                    'showroom_id' => $showroom_id,
                    'phone' => $phone,
                    'line_number' => $response->to->line_number ?? null,
                    'location' => $location,
                    'entry_id' => $entryId,
                ]);
            }

            if ($gotCallLock) {
                $line_number = $response->to->line_number ?? null;
                $site_phone = $this->findSiteByLineNumber($line_number);

                if (!empty($site_phone) && $line_number && str_contains((string) $line_number, 'mangosip.ru')) {
                    $line_number = $site_phone->phone;
                }

                if (empty($site_phone)) {
                    $this->mangoLog('notice', 'Unknown line number', [
                        'showroom_id' => $showroom_id,
                        'line_number' => $line_number,
                    ]);
                }
                if (empty($site_phone) && (int) $id === 8) {
                    $site_phone = Site::where('id', 2013)->first();
                }
                $def = substr($phone, 1, 3);
                $last = substr($phone, -7, 7);
                $info = PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
                $order = Order::where('showroom_id', $showroom_id)->phone4($phone)->with(['operator', 'region', 'status'])->first();
                if (empty($order)) {
                    $order = Order::where('showroom_id', $showroom_id)
                        ->where('source_id', 20)
                        ->where('created_at', '>=', now()->subMinutes(3))
                        ->where('phone', $phone)
                        ->with(['operator', 'region', 'status'])
                        ->latest()
                        ->first();
                }
                if (empty($info)) {
                    $this->mangoLog('warning', 'empty region phone', [
                        'phone' => $phone,
                        'raw_phone' => $rawPhone,
                    ]);
                }
                // Не создаём заявку source=20, если номера нет в плане нумерации РФ (754/764 и т.п.).
                if (empty($order) && empty($info)) {
                    $this->mangoLog('info', 'call skip create, number not in RU plan', [
                        'showroom_id' => $showroom_id,
                        'raw_phone' => $rawPhone,
                        'phone' => $phone,
                        'entry_id' => $entryId,
                    ]);
                } elseif (empty($order)) {
                    $this->mangoLog('warning', 'phone not found, creating order', [
                        'showroom_id' => $showroom_id,
                        'phone' => $phone,
                    ]);

                    $order = new Order();
                    $order->client_name = "Новый клиент";
                    $order->showroom_id = $showroom_id;
                    $order->status_id = 1;
                    $order->phone = $phone;
                    $order->site_id = $site_phone->id ?? null;
                    $order->source_id = 20; //Источник звонок
                    $order->line_number = $line_number;

                    $currentDateTime = Carbon::now();
                    $isBetween8and20 = $currentDateTime->isBetween(
                        Carbon::createFromTime(8, 0, 0),
                        Carbon::createFromTime(20, 0, 0),
                        true
                    );

                    if ($isBetween8and20) {
                        $order->operator_id = 1000;
                    }

                    $order->comment = (@$info->region !== null ? @$info->region->name : null) . " " . Carbon::createFromTimestamp($response->timestamp)->format('d.m.Y H:i:s');
                    $order->save();
                    try {
                        OrderCreated::dispatch($order, $showroom_id);
                    } catch (Throwable $e) {
                        $this->mangoLog('error', 'OrderCreated broadcast failed', [
                            'order_id' => $order->id,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }

                if (!empty($order)) {
                    $data = array(
                        "phone" => $phone,
                        "phone_region" => $info->region ?? null,
                        "region" => $order->region ?? null,
                        "line_number" => $line_number ?? null,
                        "client_name" => $order->client_name ?? null,
                        "operator" => $order->operator ?? null,
                        "status" => $order->status ?? null,
                        "work_place" => $response->to->extension ?? null,
                        "order_id" => $order->id ?? null,
                        "showroom_id" => $order->showroom_id ?? null,
                        "site_id" => $site_phone->id ?? null,
                        "site_name" => $site_phone->title ?? 'Сайт не определён',
                        "date" => $order->created_at ?? null
                    );
                    try {
                        MangoIncome::dispatch($data, $showroom_id);
                    } catch (Throwable $e) {
                        $this->mangoLog('error', 'MangoIncome broadcast failed', [
                            'order_id' => $order->id ?? null,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }

            //income
            if ($phone && $response->call_state === "Connected" && $response->location === "abonent" && (int) $response->seq === 2) {


                //Log::warning("income:" . $phone);
                //$site_phone = Site::where('phone', $response->to->line_number)->first();

                //$order2 = Order::search($phone)->where('showroom_id', $id)->where('operator_id', 1000)->latest()->first();
                sleep(3);
                $currentDateTime = Carbon::now();

                // Calculate the time 2 minutes ago

                $sixtySecondsAgo = $currentDateTime->subSeconds(60);
                $order2 = Order::where('source_id', 20)->where('showroom_id', $showroom_id)->phone4($phone)->where('created_at', '>=', $sixtySecondsAgo)->first();
                if (!empty($order2)) {
                    Log::warning("not empty order:" . $phone);
                    $operator = User::where('work_place', $response->to->extension)->where('showroom_id', $id)->whereHas('operatorSchedule', function ($query) {
                        $query->where(strtolower($this->dayName), "1");
                    })->latest('updated_at')->first();


                    if (!empty($operator)) {
                        try {
                            Log::warning('Работает-' . $response->to->extension . ' - ' . $operator->id . ' - ' . $operator->first_name);
                        } catch (Throwable $e) {
                            Log::error($e);
                        }
                        //$info = $this->getPhoneRegion($phone);
                        //Log::warning($operator);
                        $order2->operator_id = $operator->id;
                        $order2->source_id = 25;
                        $order2->save();
                        OrderProcessed::dispatch($order2, $showroom_id);
                        try {
                            // 5-income, 6-outgoing, 7-missed, 8-record
                            ProcessCall::dispatch($order2, $operator->id, 5);
                        } catch (Throwable $e) {
                            Log::error('ProcessCall1 error - ' . $e);
                        }

                        try {
                            $properties = ['create_time' => $response->timestamp, 'line_number' => $response->line_number ?? null, 'ext' => $response->to->extension ?? null];
                            $this->saveCallHistory($properties, 5, $order2->id, $response->entry_id, $operator->id ?? null);   // 5-income, 6-outgoing, 7-missed, 8-record
                        } catch (Throwable $e) {
                            Log::critical($e);
                        }
                        //ClearNotify::dispatch($order, $id);
                    } else {
                        //Log::warning($operator);
                        Log::warning('empty operator ' . $showroom_id . ' ' . $response->to->extension);
                        //$operator2 = User::where('work_place', $response->to->extension)->where('showroom_id', $id)->latest('updated_at')->first();
                        $dayName = Carbon::now()->format('l');
                        $operator2 = User::where('work_place', $response->to->extension)->where('showroom_id', $id)->whereHas('operatorSchedule', function ($query) use ($dayName) {
                            $query->where(strtolower($dayName), "1");
                        })->latest()->first();
                        if (!empty($operator2)) {
                            $order2->operator_id = $operator2->id;
                            $order2->source_id = 25;
                            $order2->save();
                            OrderProcessed::dispatch($order2, $showroom_id);
                            try {
                                // 5-income, 6-outgoing, 7-missed, 8-record
                                ProcessCall::dispatch($order2, $operator2->id, 5);
                            } catch (Throwable $e) {
                                Log::error('ProcessCall2 error - ' . $e);
                            }

                            //ClearNotify::dispatch($order, $id);
                            try {
                                Log::warning('Не работает но поднял(a)-' . $response->to->extension . ' - ' . $operator2->id . ' - ' . $operator2->first_name);
                            } catch (Throwable $e) {
                                Log::error($e);
                            }
                        } else {
                            Log::warning('Не найдено. ' . $response->to->extension . ' ' . strtolower($this->dayName) . ' orderId' . $order2->id);
                            $order2->operator_id = null;
                            $order2->source_id = 25;
                            $order2->save();
                            OrderCreated::dispatch($order2, $showroom_id);
                            // 5-income, 6-outgoing, 7-missed, 8-record
                            ProcessCall::dispatch($order2, $operator2->id ?? null, 5);

                        }
                        try {
                            $properties = ['create_time' => $response->timestamp, 'line_number' => $response->line_number ?? null, 'ext' => $response->to->extension ?? null];
                            $this->saveCallHistory($properties, 5, $order2->id, $response->entry_id, $operator2->id ?? null);   // 5-income, 6-outgoing, 7-missed, 8-record
                        } catch (Throwable $e) {
                            Log::critical($e);
                        }
                    }


                } else {
                    Log::warning('empty order ' . $showroom_id . ' ' . $response->to->extension . ' - ' . $phone);
                }
            }
            /*
            if (strlen($phone) === 11 && $response->call_state === "Disconnected") {
                Log::info('resp dilo:', (array)$response);
                if ($response->location === "queue" || $response->location === "ivr") {
                    $order44 = Order::where('operator_id', 1000)->where('showroom_id', $id)->where('phone', '+' . $phone)->orWhere('phone', $phone)->latest()->first();
                    if (!empty($order44)) {
                        Log::info('Order:'. $order44->id . ' operator_id -  ' . $order44->operator_id);
                    }
                }
            } */

        } catch (Throwable $e) {
            $this->mangoLog('error', 'call error', [
                'showroom_id' => $id,
                'message' => $e->getMessage(),
            ]);
        }


    }

    public function saveCallHistory($properties, $type, $order_id = null, $entry_id = null, $user_id = null)
    {

        try {
            activity()
                ->withProperties($properties)->tap(function (Activity $activity) use ($type, $order_id, $entry_id, $user_id) {
                    $activity->subject_type = 2;
                    $activity->subject_id = $order_id;
                    $activity->causer_type = null;
                    $activity->entry_id = $entry_id;
                    $activity->causer_id = $user_id ?? (Auth::id() ?? null);
                })
                ->log($type);
        } catch (Throwable $e) {
            Log::error("saveCallHistory:". $e->getMessage());
        }

    }

    public function record_added($id)
    {
        try {
            MangoHelper::setApiKey(config('mango.api_key_' . $id))->setApiSalt(config('mango.api_salt_' . $id));
            $response = MangoHelper::getMethodData();
            $this->mangoLog('info', 'record_added', [
                'showroom_id' => $id,
                'response' => $response,
            ]);

            if (isset($response->recording_id)) {
                ProcessRecord::dispatch($response->entry_id, $response->recording_id)->delay(now()->addMinutes(8));
                $this->mangoLog('info', 'record dispatched', [
                    'entry_id' => $response->entry_id,
                    'recording_id' => $response->recording_id,
                ]);
            }
        } catch (Throwable $e) {
            $this->mangoLog('error', 'record_added error', [
                'showroom_id' => $id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function makeCall(Request $request)
    {
        //return response()->json($request);
        MangoHelper::setApiKey(config('mango.api_key_' . $request->showroom_id))->setApiSalt(config('mango.api_salt_' . $request->showroom_id));
        $response = MangoHelper::sendCall($request->ext_number, $request->phone);
        //Log::debug($response);
        return response()->json($response);
    }

    public function getPhoneRegion($phone)
    {
        $def = substr($phone, 1, 3);
        $last = substr($phone, -7, 7);
        return PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
    }

    protected function normalizeMangoPhone($number): ?string
    {
        $number = trim((string) $number);
        if ($number === '') {
            return null;
        }
        // sip:line75@vpbx400371758.mangosip.ru → цифры 75400371758, это линия ВАТС, не клиент.
        if (str_contains($number, 'sip:') || str_contains($number, '@') || str_contains($number, 'mangosip.ru')) {
            return null;
        }

        return GeneralHelper::normalizePlus7Phone($number);
    }

    protected function findSiteByLineNumber($lineNumber): ?Site
    {
        if (empty($lineNumber)) {
            return null;
        }

        $lineNumber = (string) $lineNumber;
        $isSip = str_contains($lineNumber, 'sip:')
            || str_contains($lineNumber, '@')
            || str_contains($lineNumber, 'mangosip.ru');

        $sipPart = $lineNumber;
        $sipUser = null;
        if ($isSip) {
            $sipPart = str_contains($lineNumber, 'sip:')
                ? Str::after($lineNumber, 'sip:')
                : $lineNumber;
            $sipUser = Str::contains($sipPart, '@')
                ? Str::before($sipPart, '@')
                : $sipPart;
        }

        $normalized = $isSip ? null : $this->normalizeMangoPhone($lineNumber);
        $last10 = $normalized ? substr($normalized, -10) : null;

        return Site::query()
            ->where(function ($query) use ($lineNumber, $sipPart, $sipUser, $normalized, $last10) {
                $query->where('phone', $lineNumber)
                    ->orWhere('second_phone', $lineNumber)
                    ->orWhere('sip', $lineNumber)
                    ->orWhere('sip', $sipPart)
                    ->orWhere('sip', 'LIKE', '%' . $sipPart . '%');
                if ($sipUser) {
                    $query->orWhere('sip', $sipUser)
                        ->orWhere('sip', 'LIKE', 'sip:' . $sipUser . '@%')
                        ->orWhere('sip', 'LIKE', $sipUser . '@%');
                }
                if ($normalized) {
                    $query->orWhere('phone', $normalized)
                        ->orWhere('second_phone', $normalized)
                        ->orWhere('phone', '8' . substr($normalized, 1))
                        ->orWhere('second_phone', '8' . substr($normalized, 1));
                }
                if ($last10) {
                    $query->orWhere('phone', 'LIKE', '%' . $last10)
                        ->orWhere('second_phone', 'LIKE', '%' . $last10);
                }
            })
            ->latest()
            ->first();
    }

}
