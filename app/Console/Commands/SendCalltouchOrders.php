<?php

namespace App\Console\Commands;


use App\Models\CalltouchOrder;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendCalltouchOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calltouch:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send CallTouch Orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $orders = Order::with(['site', 'operator', 'status', 'type', 'arrival_status', 'trash'])
            ->whereHas('site', function ($query) {
                $query->where('agency_id', 14)
                    ->whereNotNull('calltouch_site_id')
                    ->whereNotNull('calltouch_access_key');
            })
            ->where('created_at', '>=', now()->subMinutes(2)) // фильтр по времени
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get()
            ->groupBy('site_id');
        ///Log::info('Order cooooount: ' . count($orders));


        foreach ($orders as $siteOrders) {

            // Разбиваем каждую группу по 100 заявок
            $siteOrders->chunk(100)->each(function ($chunkedOrders) {
                $payload = [
                    'crm' => 'MY CRM',
                    'orders' => [],
                ];

                $calltouchOrders = [];

                foreach ($chunkedOrders as $order) {
                    $orderCheck = CalltouchOrder::where('status', $order->status?->name)->where('order_id', $order->id)->exists();
                    if ($orderCheck) {
                        continue;
                    }
                    if ($order->status_id === 6 && $order->arrival_status !== null) {
                        $status = $order->status?->name . ' ' . $order->arrival_status->name;
                    } else if ($order->status_id === 8 && $order->trash !== null) {
                        $status = $order->status?->name . ' ' . $order->trash->name;
                    } else {
                        $status = $order->status?->name;
                    }


                    $commentParts = ["Заявка со статусом " . $status];


                    if ($order->mark?->name) {
                        $commentParts[] = 'Марка: ' . $order->mark?->name;
                    }
                    if ($order->model?->name) {
                        $commentParts[] = 'Модель: ' . $order->model?->name;
                    }
                    if ($order->car_year) {
                        $commentParts[] = 'Год: ' . $order->car_year;
                    }
                    if ($order->car_status_id) {
                        $statusText = $order->car_status_id === 1 ? 'Новая' : ($order->car_status_id === 2 ? 'БУ' : null);
                        if ($statusText) {
                            $commentParts[] = 'Статус: ' . $statusText;
                        }
                    }
                    if ($order->complectation) {
                        $commentParts[] = 'Комплектация: ' . $order->complectation;
                    }
                    if ($order->price) {
                        $commentParts[] = 'Цена: ' . number_format((float)preg_replace('/[^\d.]/', '', $order->price), 2, '.', '');
                    }

                    $comment = implode(', ', $commentParts);

                    $matching = [
                        [
                            'type' => 'leadContact',
                            'leadContactParams' => [
                                'phones' => array_filter([
                                    $order->phone,
                                    $order->phone_2,
                                    $order->phone_3,
                                ]),
                                'date' => $order->created_at->format('d-m-Y H:i:s'),
                                'searchDepth' => 262800,
                                'leadTypeToMatch' => 'nearestUniq',
                            ],
                        ],
                        [
                            'type' => 'withoutSource',
                        ],
                    ];

                    if ($order->utm_source || $order->utm_medium || $order->utm_campaign || $order->utm_content || $order->utm_term) {
                        $matching[] = [
                            'type' => 'customSources',
                            'customSourceParams' => [
                                'source' => $order->utm_source ?? '',
                                'medium' => $order->utm_medium ?? '',
                                'campaign' => $order->utm_campaign ?? '',
                                'content' => $order->utm_content ?? '',
                                'term' => $order->utm_term ?? '',
                            ],
                        ];
                    }


                    $orderPayload = null;


                    if (
                        !empty($order->utm_source) ||
                        !empty($order->utm_medium) ||
                        !empty($order->utm_campaign) ||
                        !empty($order->utm_content) ||
                        !empty($order->utm_term)
                    ) {
                        $orderPayload = [
                            'source' => $order->utm_source ?? '',
                            'medium' => $order->utm_medium ?? '',
                            'campaign' => $order->utm_campaign ?? '',
                            'content' => $order->utm_content ?? '',
                            'term' => $order->utm_term ?? '',
                        ];
                    }



                    $payload['orders'][] = [
                        'matching' => $matching,
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
                        "comment" => [
                            "text" => $comment
                        ],
                        "addTags" => [
                            ["tag" => $status]
                        ],
                        'orderContacts' => [
                            'mainContacts' => [
                                'fio' => $order->client_name,
                                'phoneNumber' => $order->phone,
                                'email' => $order->email ?? null,
                            ],
                        ],
                        "customSources" => $orderPayload


                    ];

                    $calltouchOrders[] = [
                        'order_id' => $order->id,
                        'status' => $status,
                        'sent_at' => Carbon::now(),
                    ];
                }



                $first = $chunkedOrders->first();
                $accessToken = $first->site?->calltouch_access_key;
                $siteId = $first->site?->calltouch_site_id;

                Log::info('Payload CReate', $payload);
                // Log::info($accessToken . ' - ' . $siteId);
                if ($accessToken && $siteId && count($payload['orders']) > 0) {
                    $response = Http::withHeaders([
                        'Access-Token' => $accessToken,
                        'SiteId' => $siteId,
                    ])->post('https://api.calltouch.ru/lead-service/v1/api/client-order/create', $payload);

                    $decodedResponse = json_decode($response->body(), true);
                    Log::error('Response from Calltouch', is_array($decodedResponse) ? $decodedResponse : ['response' => $response->body()]);


                    if ($response->successful()) {
                        CalltouchOrder::insert($calltouchOrders);
                    } else {
                        Log::error('Ошибка отправки в Calltouch', [
                            'site_id' => $siteId,
                            'response' => $response->body(),
                        ]);
                    }
                }
            });
        }
        return 0;
    }
}
