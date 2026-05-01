<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CalltouchUpdateDeal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalltouchDealUpdateService
{
    public function update(Order $order): void
    {



        $site = $order->site;

        if (!$site?->calltouch_site_id || !$site?->calltouch_access_key) {
            Log::warning("Calltouch site ID or token missing for order {$order->id}");
            return;
        }

        $payload = $this->buildPayload($order);

        $response = Http::withHeaders([
            'Access-Token' => $site->calltouch_access_key,
            'SiteId' => $site->calltouch_site_id,
        ])->post('https://api.calltouch.ru/lead-service/v1/api/client-order/update', $payload);

        $decoded = json_decode($response->body(), true);
        Log::info('Calltouch response1', is_array($decoded) ? $decoded : ['body' => $response->body()]);

        if ($response->successful()) {

            CalltouchUpdateDeal::updateOrCreate(
                ['order_id' => $order->id],
                ['status_id' => $order->status_id, 'updated_at' => now()]
            );
        } else {
            Log::error('Calltouch update failed', [
                'site_id' => $site->calltouch_site_id,
                'response' => $response->body(),
            ]);
        }
    }

    private function buildPayload(Order $order): array
    {
        $status = match (true) {
            $order->status_id === 6 && $order->arrival_status !== null => $order->status?->name . ' ' . $order->arrival_status?->name,
            $order->status_id === 8 && $order->trash !== null => $order->status?->name . ' ' . $order->trash?->name,
            default => $order->status?->name,
        };

        $commentParts = [
            "Заявка со статусом " . $status,
            $order->mark?->name ? 'Марка: ' . $order->mark?->name : null,
            $order->model?->name ? 'Модель: ' . $order->model?->name : null,
            $order->car_year ? 'Год: ' . $order->car_year : null,
            $order->car_status_id ? 'Статус: ' . ($order->car_status_id === 1 ? 'Новая' : 'БУ') : null,
            $order->complectation ? 'Комплектация: ' . $order->complectation : null,
            $order->price ? 'Цена: ' . number_format((float)preg_replace('/[^\d.]/', '', $order->price), 2, '.', '') : null,
        ];

        $comment = implode(', ', array_filter($commentParts));

        $rematching = [
            [
                'type' => 'leadContact',
                'leadContactParams' => [
                    'phones' => array_filter([$order->phone, $order->phone_2, $order->phone_3]),
                    'date' => $order->created_at->format('d-m-Y H:i:s'),
                    'leadTypeToMatch' => 'nearestUniq',
                    'searchDepth' => 262800,
                ],
            ],
            ['type' => 'withoutSource'],
        ];

        if ($order->utm_source || $order->utm_medium || $order->utm_campaign || $order->utm_content || $order->utm_term) {
            $rematching[] = [
                'type' => 'customSources',
                'customSourceParams' => [
                    'source'   => mb_substr($order->utm_source ?? '', 0, 126),
                    'medium'   => mb_substr($order->utm_medium ?? '', 0, 126),
                    'campaign' => mb_substr($order->utm_campaign ?? '', 0, 126),
                    'content'  => mb_substr($order->utm_content ?? '', 0, 126),
                    'term'     => mb_substr($order->utm_term ?? '', 0, 126),
                ],
            ];
        }

        return [
            'crm' => 'MY CRM',
            'orders' => [[
                'reMatching' => $rematching,
                'orderNumber' => $order->id,
                'funnel' => 'Воронка сделка',
                'status' => $status,
                'statusDate' => $order->updated_at->format('d-m-Y H:i:s'),
                'updateDate' => $order->updated_at->format('d-m-Y H:i:s'),
                'orderDate' => $order->created_at->format('d-m-Y H:i:s'),
                'currency' => 'rub',
                'revenue' => "1",
                'margin' => "1",
                'orderLink' => null,
                'orderName' => $order->type?->name,
                'manager' => $order->operator?->first_name . ' ' . $order->operator?->last_name,
                'addNewComment' => [ 'text' => mb_substr($comment, 0, 255)],

                'addTags' => ['overwrite' => true, 'tags' => [['tag' => $status]]],
                'orderContacts' => [
                    'mainContacts' => [
                        'fio' => $order->client_name,
                        'phoneNumber' => $order->phone,
                        'email' => $order->email,
                    ],
                ],
            ]]
        ];
    }
}

