<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\Site;
use Brick\Math\Exception\NumberFormatException;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportAgencyController extends Controller
{
    public function import()//: JsonResponse
    {
        $sites = Site::whereIn('agency_id', [7, 10])->where('active', 1)->whereNotNull('token')->with(['request'])->get();


        if (count($sites) > 0) {
            foreach ($sites as $site) {

                if (isset($site->request->last_request_time)) {
                    $last_request = $site->request->last_request_time->format('Y-m-d H:i:s');

                } else {
                    $last_request = Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s');
                }
                $response = null;
                try {
                    $response = Http::get($site->post_url . '?from=' . $last_request . '&secret=' . $site->token);
                    DB::table('last_request_time')->updateOrInsert(
                        ['site_id' => $site->id], // Add the site_id as the first argument
                        ['last_request_time' => now()]
                    );
                } catch (Throwable $e) {
                    Log::emergency("last_request_time--". $e);
                }



                try {
                    $items = $response->json();

                    if (is_array($items) && array_is_list($items) && !empty($items)) {

                        foreach ($items as $item) {

                            try {
                                //Log::error("count items:", count($items));
                                //Log::emergency($item);
                                $order = new Order;
                                $order->client_name = $item['client_first_name'] ?? 'Новый клиент';
                                $order->showroom_id = $site->showroom_id;
                                $order->site_id = $site->id ?? null;
                                $order->status_id = 1;

                                try {
                                    $order->phone = PhoneNumber::make($item['client_phone_number'], 'RU')->formatE164();
                                } catch (Throwable $e) {
                                    Log::error("client_phone_number error", [$item]);
                                    $order->phone = preg_replace('/[^0-9+]/', '', $item['client_phone_number']) ?? null;
                                }

                                try {
                                    $order->type_id = GeneralHelper::getType($item['type']);
                                    if (isset($item['car_brand']) || isset($item['car_model'])) {
                                        $car = GeneralHelper::getCar($item['car_brand'], $item['car_model']);
                                        $order->mark_id = $car->brand_id ?? null;
                                        $order->model_id = $car->id ?? null;
                                    }
                                } catch (Throwable $e) {
                                    Log::error("type error " . $e,);
                                }
                                $order->price = $item['car_price'] ?? null;
                                //$order->ip_address = $item['client_ip'] ?? null;
                                $order->uuid = $item['uuid'] ?? null;
                                $order->referrer = $item['referrer'] ?? null;
                                $order->comment = $item['comment'] ?? null;
                                $order->complectation = $item['car_configuration'] ?? null;
                                $order->source_id = 6;
                                $order->save();
                                OrderCreated::dispatch($order);

                            } catch (Throwable $e) {
                                Log::emergency($e);
                                Log::error("agency error", [$item]);
                                Log::error("agency error", [$site]);
                                continue;

                            }

                        }

                    }


                } catch (Throwable $e) {
                    Log::emergency($e);
                    continue;
                }

            }
            return response()->json("Done");

        }

    }


    public function test(Request $request)//: JsonResponse
    {
        $sites = Site::whereIn('agency_id', [7, 10])->where('id', $request->id)->where('active', 1)->whereNotNull('token')->with(['request'])->get();

        //return $sites;


        if (count($sites) > 0) {

            foreach ($sites as $site) {

               // return $site;

                if (isset($site->request->last_request_time)) {
                    //Log::alert('last_request_time');
                    $last_request = $site->request->last_request_time->format('Y-m-d H:i:s');

                } else {
                    $last_request = Carbon::now()->subMinutes(1000)->format('Y-m-d H:i:s');
                }
                $last_request = Carbon::now()->subMinutes(1000)->format('Y-m-d H:i:s');

                Log::emergency($last_request);


                $response = Http::get($site->post_url . '?from=' . $last_request . '&secret=' . $site->token);
                //return $response;

                    //return  $items;
                $items = $response->json();



                //Log::emergency($items);

                Log::alert("count_orders_" . $site->id);

                try {

                    if (is_array($items) && count($items) > 0) {

                        return $items;

                        Log::error("items:", [$items]);

                        foreach ($items as $item) {
                            try {
                                //Log::emergency($item);
                                $order = new Order;
                                $order->client_name = $item['client_first_name'] ?? 'Новый клиент';
                                $order->showroom_id = $site->showroom_id;
                                $order->site_id = $site->id ?? null;
                                $order->status_id = 1;
                                try {
                                    $order->phone = PhoneNumber::make($item['client_phone_number'], 'RU')->formatE164();
                                } catch (Throwable $e) {
                                    $order->phone = preg_replace('/[^0-9+]/', '', $item['client_phone_number']) ?? null;
                                }
                                $order->type_id = GeneralHelper::getType($item['type']);
                                if (isset($item['car_brand']) || isset($item['car_model'])) {
                                    $car = GeneralHelper::getCar($item['car_brand'], $item['car_model']);
                                    $order->mark_id = $car->brand_id ?? null;
                                    $order->model_id = $car->id ?? null;
                                }
                                $order->price = $item['car_price'] ?? null;
                                //$order->ip_address = $item['client_ip'] ?? null;
                                $order->referrer = $item['referrer'] ?? null;
                                $order->comment = $item['comment'] ?? null;
                                $order->complectation = $item['car_configuration'] ?? null;
                                $order->source_id = 6; //Agency New
                                $order->save();
                                OrderCreated::dispatch($order);

                            } catch (Throwable $e) {
                                Log::emergency($e);
                                Log::error("agency error", [$item]);
                                Log::error("agency error", [$site]);
                                continue;

                            }

                        }

                    } else {
                        return "error";
                    }


                } catch (Throwable $e) {
                    Log::emergency($e);
                }

            }
            return response()->json("Done");

        }

    }
}
