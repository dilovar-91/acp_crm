<?php
// app/Console/Commands/MangoDealOrders.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\CalltouchOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MangoDealOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mango:deal-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create deals in Mango CallTracking';

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
     * @return mixed
     */
    public function handle()
    {
        $orders = Order::with(['site', 'status', 'arrival_status', 'trash'])
            ->whereHas('site', function ($query) {
                $query->where('agency_id', 14)
                    ->whereNotNull('mango_site_id')
                    ->whereNotNull('mango_token');
            })
            ->where('created_at', '>=', now()->subMinutes(2)) // фильтр по времени
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get()
            ->groupBy('site_id');

        foreach ($orders as $siteOrders) {


            $siteOrders->each(function ($order) {
                // Проверка на дубликат
                $exists = CalltouchOrder::where('status', $order->status?->name)
                    ->where('order_id', $order->id)
                    ->exists();

                if ($exists) {
                    return;
                }

                // Формирование статуса
                if ($order->status_id === 6 && $order->arrival_status !== null) {
                    $status = $order->status?->name . ' ' . $order->arrival_status->name;
                } elseif ($order->status_id === 8 && $order->trash !== null) {
                    $status = $order->status?->name . ' ' . $order->trash->name;
                } else {
                    $status = $order->status?->name;
                }

                // Подготовка payload
                $dealPayload = [
                    [
                        'id' => (string)$order->id,
                        'status' => 'new',
                        'timestamp' => $order->updated_at->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'),
                        'amount' => 1,
                        'contactPhone' => $order->phone,
                        'gaCid' => null,
                        'utmSource' => mb_substr($order->utm_source ?? '', 0, 126),
                        'utmMedium' => mb_substr($order->utm_medium ?? '', 0, 126),
                        'utmCampaign' => mb_substr($order->utm_campaign ?? '', 0, 126),
                        'utmContent' => mb_substr($order->utm_content ?? '', 0, 126),
                        'utmTerm' => mb_substr($order->utm_term ?? '', 0, 126),
                        'regionCode' => null,
                        'firstPath' => $order->entry_point,
                    ]
                ];

                $token = $order->site?->mango_token;
                $siteId = $order->site?->mango_site_id;

                if (!$token || !$siteId) {
                    Log::warning('Mango site token or ID missing', ['order_id' => $order->id]);
                    return;
                }

                $url = "https://widgets-api.mango-office.ru/v1/calltracking/{$siteId}/deals";


                // Log::info('Mango payload (JSON)', $dealPayload);

                $response = Http::withToken($token)
                    ->acceptJson()
                    ->post($url, $dealPayload);

                Log::info('Mango DKT API response', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->successful()) {
                    CalltouchOrder::create([
                        'order_id' => $order->id,
                        'status' => $status,
                        'sent_at' => now(),
                    ]);
                } else {
                    Log::info('Ошибка отправки сделки в Mango Office DKT', [
                        'order_id' => $order->id,
                        'response' => $response->body(),
                    ]);
                }
            });
        }
    }
}
