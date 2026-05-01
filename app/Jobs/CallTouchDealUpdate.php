<?php

namespace App\Jobs;

use App\Models\CallCount;
use App\Models\CalltouchUpdateDeal;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// TODO remove this file after month
class CallTouchDealUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Отложенный отчет по кол-во звонков.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::error('Job started calltouch' . $this->order->id);
        $payload = [
            'crm' => 'MY CRM',
            'orders' => [],
        ];


        $orderItem = Order::with(['status', 'mark', 'model', 'type', 'operator', 'site'])->find($this->order->id);
        $hasCalltouchSiteId = $orderItem?->site?->calltouch_site_id ? true : false;

        if (empty($orderItem) || !$hasCalltouchSiteId) {
            Log::error('return hasCalltouchSiteId');


            return;
        }

        /*
                $orderCheck = CalltouchUpdateDeal::where('status_id', $orderItem->status_id)->where('order_id', $orderItem->id)->exists();
                if ($orderCheck) {
                    return;
                }*/

        if ($orderItem->status_id === 6 && $orderItem->arrival_status !== null) {
            $status = $orderItem->status?->name . ' ' . $orderItem->arrival_status->name;
        }  else if ($orderItem->status_id === 8 && $orderItem->trash !== null) {
            $status = $orderItem->status?->name . ' ' . $orderItem->trash->name;
        }
        else {
            $status = $orderItem->status?->name;
        }

        $commentParts = ["Заявка со статусом " . $status];

        if ($orderItem->mark?->name) {
            $commentParts[] = 'Марка: ' . $orderItem->mark?->name;
        }
        if ($orderItem->model?->name) {
            $commentParts[] = 'Модель: ' . $orderItem->model?->name;
        }
        if ($orderItem->car_year) {
            $commentParts[] = 'Год: ' . $orderItem->car_year;
        }
        if ($orderItem->car_status_id) {
            $statusText = $orderItem->car_status_id === 1 ? 'Новая' : ($orderItem->car_status_id === 2 ? 'БУ' : null);
            if ($statusText) {
                $commentParts[] = 'Статус: ' . $statusText;
            }
        }




        if ($orderItem->complectation) {
            $commentParts[] = 'Комплектация: ' . $orderItem->complectation;
        }
        if ($orderItem->price) {
            $commentParts[] = 'Цена: ' . number_format((float)preg_replace('/[^\d.]/', '', $orderItem->price), 2, '.', '');
        }

        $comment = implode(', ', $commentParts);

        $rematching = [
            [
                'type' => 'leadContact',
                'leadContactParams' => [
                    'phones' => array_filter([
                        $orderItem->phone,
                        $orderItem->phone_2,
                        $orderItem->phone_3,
                    ]),
                    'date' => $orderItem->created_at->format('d-m-Y H:i:s'),
                    'leadTypeToMatch' => 'nearestUniq',
                    'searchDepth' => 262800,
                ],
            ],
            [
                "type" => "withoutSource"
            ]
        ];

        if ($orderItem->utm_source || $orderItem->utm_medium || $orderItem->utm_campaign || $orderItem->utm_content || $orderItem->utm_term) {
            $rematching[] = [
                'type' => 'customSources',
                'customSourceParams' => [
                    'source' => $orderItem->utm_source ?? '',
                    'medium' => $orderItem->utm_medium ?? '',
                    'campaign' => $orderItem->utm_campaign ?? '',
                    'content' => $orderItem->utm_content ?? '',
                    'term' => $orderItem->utm_term ?? '',
                ],
            ];
        }


        $payload['orders'][] = [
            'reMatching' => $rematching,
            'orderNumber' => $orderItem->id,
            'funnel' => 'Воронка сделка',
            'status' => $status,
            'statusDate' => $orderItem->updated_at->format('d-m-Y H:i:s'),
            'updateDate' => $orderItem->updated_at->format('d-m-Y H:i:s'),
            'orderDate' => $orderItem->created_at->format('d-m-Y H:i:s'),
            'currency' => 'rub',
            'revenue' => "1",
            'margin' => "1",
            'orderLink' => null,
            'orderName' => $orderItem->type?->name,
            'manager' => $orderItem->operator?->first_name . ' ' . $orderItem->operator?->last_name,
            'addNewComment' => [
                'text' => $comment,
            ],
            'addTags' => [
                'overwrite' => true,
                'tags' => [
                    ['tag' => $status]
                ],
            ],
            'orderContacts' => [
                'mainContacts' => [
                    'fio' => $orderItem->client_name,
                    'phoneNumber' => $orderItem->phone,
                    'email' => $orderItem->email ?? null,
                ],
            ],
        ];


        $accessToken = $orderItem->site?->calltouch_access_key;
        $siteId = $orderItem->site?->calltouch_site_id;

        ///Log::info('Payload Deal Update', $payload);

        Log::error('Payload DealUpdate', $payload);
        //Log::info($accessToken . ' - ' . $siteId);
        if ($accessToken && $siteId && count($payload['orders']) > 0) {
            $response = Http::withHeaders([
                'Access-Token' => $accessToken,
                'SiteId' => $siteId,
            ])->post('https://api.calltouch.ru/lead-service/v1/api/client-order/update', $payload);

            $decodedResponse = json_decode($response->body(), true);
            Log::error('RespCT Deal Update', is_array($decodedResponse) ? $decodedResponse : ['response' => $response->body()]);


            if ($response->successful()) {
                CalltouchUpdateDeal::updateOrCreate(
                    [
                        'order_id' => $orderItem->id,
                    ],
                    [
                        'status_id' => $orderItem->status_id,
                        'updated_at' => now()
                    ]
                );
            } else {
                Log::error('Ошибка отправки в Calltouch Deal Update', [
                    'site_id' => $siteId,
                    'response' => $response->body(),
                ]);
            }
        } else {
            Log::warning('Нет данных Calltouch Deal Update для сайта или заявок нету', [
                'site_id' => $orderItem->site_id,
            ]);
        }
    }
}
