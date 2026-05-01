<?php

namespace App\Http\Controllers\Victory;

use App\Events\OrderCreated;
use App\Helpers\ShowroomHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\PassedOrder;
use App\Models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class PassAoController extends Controller
{



    public function execute()//: JsonResponse
    {
        $orders = Order::select(['id', 'site_id', 'phone', 'utm_source', 'showroom_id'])->whereIn('showroom_id', [18,19,20,21,22,23,24,25,26,28])->where('status_id', 14)->whereNotNull('utm_source')->whereDoesntHave('passedOrder')->whereHas('site', function ($query) {
            $query->whereIn('agency_id', [18, 19, 9, 3, 13]);
        })->with(['site'])->whereDate('created_at', '>=', now()->subDays(2))->limit(150)->get();

        //return $orders;

        //$orders = Order::where('status_id', 14)->whereNotNull('utm_source')->whereDoesntHave('passedOrder')->limit(5)->get();
        if (count($orders) > 0) {
            foreach ($orders as $order) {
                try {
                    $cleanedPhone = ltrim($order->phone, '+');

                    //$showroom_id = ShowroomHelper::getShowroomKeyByValue($order->showroom_id);
                    //return $order->showroom_id .'=>'.$site_id;

                    $site = Site::where('showroom_id', $order->site->showroom_id)->whereNotNull('token')->where('agency_id', 20)->first();
                    if(empty($site)){
                        continue;
                    }
                    //return $site;
                    $params = [
                        'phone_number' => $cleanedPhone,
                        'domain' => $order->utm_source,
                        'api_key' => $site->token,
                    ];

                    //return $params;
                    $response = Http::post($site->post_url, $params);

                    if ($response->successful()) {
                        // Log::info('Request successful: ' .$order->showroom_id.'->' . $site->showroom_id .': '. json_decode('"' . $response->body() . '"'));
                        PassedOrder::create([
                            'order_id' => $order->id,
                        ]);
                    }

                    ///return $order;
                } catch (\Exception $e) {
                    Log::error('AO error: ' . $e->getMessage());
                    continue;
                }
            }
        }
    }
}
