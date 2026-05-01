<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class QuizController extends Controller
{
    public function quiz(Request $request, $showroom, $site)
    {

        $showroom_id = $showroom ?? null;
        $site_id = $site ?? null;
        $text = "";

        try {
            Log::warning($request);
        } catch (Throwable $e) {

        }
        foreach ($request['answers'] as $value) {
            if (is_array($value['a'])) {
                $text .= $value['q'] . ": ";
                foreach ($value['a'] as $answer) {
                    $text .= $answer . ",  ";
                }
                $text .= " \n ";
            } else {
                $text .= $value['q'] . ": " . $value['a'] . " \n ";
            }
        }
        $text .= "Результат опроса: \n";
        $text .= 'Заголовок:' . ($request['result']['title'] ?? '') . " \n ";
        $text .= 'Текст:' . ($request['result']['text'] ?? '') . " \n ";
        $text .= 'Стоимость:' . ($request['result']['cost'] ?? '') . " \n ";

        $order = new Order;

        $order->phone = $request['contacts']['phone'];

        try {
            $order->phone = PhoneNumber::make($request['contacts']['phone'], 'RU')->formatE164();
        } catch (Throwable $e) {
            $order->phone = preg_replace('/[^0-9+]/', '', $request['contacts']['phone']) ?? null;
        }
        $order->comment = $text;
        $order->client_name = $request['contacts']['name'] ?? 'Новый клиент';

        $order->showroom_id = $showroom_id;
        $order->site_id = $site_id;
        $order->type_id = 2;
        $order->status_id = 1;
        $order->source_id = 6;


        $url = $request['extra']['href'] ?? null;

        $order->entry_point = $url;

        try {
            if ($url) {
                $query = parse_url($url, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $params);
                    $order->utm_source   = $params['utm_source']   ?? null;
                    $order->utm_medium   = $params['utm_medium']   ?? null;
                    $order->utm_campaign = $params['utm_campaign'] ?? null;
                    $order->utm_term     = $params['utm_term']     ?? null;
                    $order->utm_content  = $params['utm_content']  ?? null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('UTM parse error', [
                'url' => $request['extra']['href'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }


        try {
            $order->save();
        } catch (Throwable $e) {
            Log::emergency($e);
            //Log::warning($request);
        }

        OrderCreated::dispatch($order);
        return response()->json(['message' => 'Success!!!'], 200);

    }
}
