<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MangoCallTrackingController extends Controller
{
    public function execute(Request $request, $showroom)
    {

        $showroom_id = $showroom ?? null;

        $params = [
            'yaCid' => $request->query('yaCid'),
            'gaCid' => $request->query('gaCid'),
            'uid' => $request->query('uid'),
            'rsCid' => $request->query('rsCid'),
            'utmSource' => $request->query('utmSource'),
            'utmMedium' => $request->query('utmMedium'),
            'utmCampaign' => $request->query('utmCampaign'),
            'utmContent' => $request->query('utmContent'),
            'utmTerm' => $request->query('utmTerm'),
            'city' => $request->query('city'),
            'device' => $request->query('device'),
            'url' => $request->query('url'),
            'firstUrl' => $request->query('firstUrl'),
            'callerNumber' => $request->query('callerNumber'),
            'number' => $request->query('number'),
            'webhookType' => $request->query('webhookType'),
        ];


        /*try {
            Log::info('CT Params', ['params' => $params, 'showroom_id' => $showroom_id]);
        } catch (\Exception $e) {

        }*/

        if (empty($params['number']) || empty($params['callerNumber'])) {
            Log::info('MangoCallTrackingController execute number &&  callerNumber is required');
            return response()->json(['error' => 'number &&  callerNumber is required'], 400);
        }

        // Найти последнюю заявку по номеру
        $order = Order::where('phone', 'like', '%' . $params['callerNumber'])
            ->where('showroom_id', $showroom_id)
            ->with('site')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Обновить UTM, если пустые
        $updated = false;
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $utmKey) {
            $camelKey = lcfirst(Str::camel($utmKey)); // e.g. utmSource
            if (empty($order->$utmKey) && !empty($params[$camelKey])) {
                $order->$utmKey = $params[$camelKey];
                $updated = true;
            }
        }

        // Добавить device и city в комментарий
        $commentAddon = '';
        if (!empty($params['device'])) {
            $commentAddon .= " Устройство: {$params['device']}.";
        }
        if (!empty($params['city'])) {
            $commentAddon .= " Город: {$params['city']}.";
        }
        if (!empty($params['url'])) {
            $order->link_1 = $params['url'];
            $commentAddon .= "URL: {$params['url']}.";
        }
        if (!empty($params['firstUrl'])) {
            $order->entry_point = $params['firstUrl'];
        }

        if (!empty($commentAddon)) {
            $order->comment = trim($order->comment . "\n" . $commentAddon);
            $updated = true;
        }

        if ($updated) {
           //  $order->source_id = 302;
            $order->save();
        }

        return response()->json(['status' => 'ok']);


    }
}
