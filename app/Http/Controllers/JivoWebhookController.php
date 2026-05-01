<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;
use Illuminate\Support\Facades\Validator;

class JivoWebhookController extends Controller
{
    public function handle(Request $request, $showroom_id, $site_id)
    {

        $validator = Validator::make($request->all(), [
            'visitor.phone' => ['required', 'string'],
        ]);

        if ($validator->fails()) {

            Log::channel('jivo')->warning('Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->all()
            ]);

            // ВАЖНО: 200, чтобы Jivo не слал повторно
            return response()->json(['ok' => true, 'validation_failed' => true], 200);
        }


        Log::channel('jivo')->info('Jivo webhook received', [
            'site_id' => $site_id,
            'showroom_id' => $showroom_id,
            'payload' => $request->all()
        ]);

        if ($request->input('event_name') !== 'chat_finished') {
            return response()->json(['ok' => true, 'ignored' => true], 200);
        }

        try {

            $chatId = $request->input('chat_id');

            $name = $request->input('visitor.name');
            $phone = $request->input('visitor.phone');
            $url = $request->input('page.url');
            $utm = $request->input('session.utm_json') ?? [];



            $comment = "Jivo chat #{$chatId}\n";
            $comment .= "Страница: {$url}\n";
            $comment .= "Заголовок: " . ($request->input('page.title') ?? '') . "\n";
            $comment .= "Чат: " . ($request->input('plain_messages') ?? '') . "\n";

            $region = $request->input('session.geoip.region');

            if (!empty($region)) {
                $comment .= "Регион: " . $region . "\n";
            }

            $order = new Order;
            $order->client_name = $name ?? 'Новый клиент';
            $order->site_id = $site_id;
            $order->status_id = 1;
            $order->type_id = 1;
            $order->source_id = 617;
            $order->showroom_id = $showroom_id;
            $order->entry_point = $url;
            $order->comment = $comment;

            try {
                $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $phone) ?? null;
            }


            $order->utm_source = $utm['source'] ?? null;
            $order->utm_medium = $utm['medium'] ?? null;
            $order->utm_term = $utm['term'] ?? null;
            $order->utm_campaign = $utm['campaign'] ?? null;
            $order->utm_content = $utm['content'] ?? null;

            $order->save();

            OrderCreated::dispatch($order);

            return response()->json(['ok' => true, 'order_id' => $order->id], 200);


        } catch (Throwable $e) {

            Log::channel('jivo')->error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_id' => $chatId,
                'site_id' => $site_id
            ]);

            // ВАЖНО: возвращаем 200, чтобы Jivo не ретраил бесконечно
            return response()->json(['ok' => false], 200);
        }
    }
}
