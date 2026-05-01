<?php

namespace App\Services;

use App\Models\CalltouchUpdateDeal;
use App\Models\Order;
use App\Models\CalltouchOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MangoDealUpdateService
{
    public function update(Order $order): void
    {

        // Пропускаем, если уже есть такая заявка и статус
        $exists = CalltouchOrder::where('status', $order->status?->name)
            ->where('order_id', $order->id)
            ->exists();
        if ($exists) {
            return;
        }


        // Подготавливаем payload
        $amount = 1;

        if ($order->status_id === 6 && ($order->arrival_id === 5 || $order->arrival_id === 2)) {
            $status = 'closed';
            $amount = 10000;
        } else if ($order->status_id === 7 || $order->status_id === 15) {
            $status = 'declined';
        } else {
            $status = 'accepted';
        }



        $dealPayload = [[
            'id' => (string)$order->id,
            'status' => $status,
            'timestamp' => $order->updated_at->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'),
            'amount' => $amount,
            'contactPhone' => $order->phone,
            'gaCid' => null,
            'utmSource' => Str::limit($order->utm_source, 110, ''),
            'utmMedium' => Str::limit($order->utm_medium, 110, ''),
            'utmCampaign' => Str::limit($order->utm_campaign, 110, ''),
            //'utmContent' => Str::limit($order->utm_content, 110, ''),
            'utmTerm' => Str::limit($order->utm_term, 110, ''),
            'regionCode' => null,
            'firstPath' => Str::limit($order->entry_point),
        ]];


        $token = $order->site?->mango_token;
        $siteId = $order->site?->mango_site_id;

        if (!$token || !$siteId) {
            Log::error('Mango site token or ID missing', ['order_id' => $order->id]);
            return;
        }

        $url = "https://widgets-api.mango-office.ru/v1/calltracking/{$siteId}/deals";


        $response = Http::withToken($token)
            ->acceptJson()
            ->post($url, $dealPayload);

        Log::info('Mango Deal Update API response', [
            'order_id' => $order->id,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->successful()) {
            CalltouchUpdateDeal::updateOrCreate(
                ['order_id' => $order->id],
                ['status_id' => $order->status_id, 'updated_at' => now()]
            );
        } else {
            Log::error('Ошибка отправки сделки в Mango Deal Update', [
                'order_id' => $order->id,
                'response' => $response->body(),
            ]);
        }
    }
}
