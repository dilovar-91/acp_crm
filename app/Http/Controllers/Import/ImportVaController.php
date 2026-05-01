<?php

namespace App\Http\Controllers\Import;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportVaController extends Controller
{

    public function import()//: JsonResponse
    {

        Log::info('dilova start');

        $lastLaunched = Cache::get('last_va_launched_at');
        if ($lastLaunched && $lastLaunched->diffInMinutes() < 6) {
            Log::info('request-va:import еще рано - '.$lastLaunched->diffInMinutes());
            return;
        }
        Cache::put('last_va_launched_at', now());
        Log::info('ImportVaController imported successfully!');
        $sites = Site::whereIn('agency_id', [9, 19, 27, 29])->where('active', 1)->whereNotNull('token')->with('request')->get();

        //return $sites;

        ///Log::emergency($sites);

        if (count($sites) > 0) {
            foreach ($sites as $site) {
                //return $site->request->last_request_time->addSeconds(2);
                if (isset($site->request->last_request_time)) {
                    $last_request = $site->request->last_request_time->addSeconds(1)->format('Y-m-d H:i:s');
                } else {
                    $last_request = Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s');
                }

                $client = new Client();
                //return $site;
                $params['form_params'] = array('token' => $site->token, 'last_request_date' => $last_request);


                $items = null;
                try {
                    $response = $client->post($site->post_url, $params);
                    //return $response;
                    $items = json_decode($response->getBody());

                    DB::table('last_request_time')->updateOrInsert(
                        ['site_id' => $site->id], // Add the site_id as the first argument
                        ['last_request_time' => Carbon::now()->subSeconds(4)->format('Y-m-d H:i:s')]
                    );
                } catch (RequestException $e) {
                    // Log any errors that occur during the request
                    $errorResponse = $e->getResponse();
                    if ($errorResponse) {
                        $errorBody = $errorResponse->getBody()->getContents();
                        Log::error("Error Response Body VA: $errorBody");
                    }

                    // Log the exception
                    Log::error("Request Exception VA: " . $e->getMessage());
                }


                //return  $items;

                if (is_array($items) && count($items) > 0) {
                    /*if ($site->showroom_id == 9){
                        Log::emergency($items);
                    }*/

                    foreach ($items as $item) {
                        try {
                        // Log::emergency($item);
                        //$mark = Brand::where('name', 'like', '%' . $item->brand . '%')->first();
                        $order = new Order;
                        $order->client_name = ((($item->last_name . ' ') ?? '') . (($item->first_name . ' ') ?? '') . (($item->middle_name . ' ') ?? '')) ?? 'Новый клиент';
                        $order->showroom_id = $site->showroom_id;
                        $order->site_id = $site->id ?? null;
                        $order->status_id = 1;

                        try {
                            $order->phone = PhoneNumber::make($item->mobile_tel, 'RU')->formatE164();
                        } catch (Throwable $e) {
                            $order->phone = preg_replace('/[^0-9+]/', '', $item->mobile_tel) ?? null;
                        }

                        $order->email = empty($item->email) ? null : $item->email;
                        try {

                            $order->type_id = GeneralHelper::getType($item->request_type);
                            if ($item->brand !== '-' || $item->model !== '-') {
                                $car = GeneralHelper::getCar($item->brand, $item->model);
                                $order->mark_id = $car->brand_id ?? null;
                                $order->model_id = $car->id ?? null;
                            }
                        } catch (Throwable $e) {
                            Log::error("type error import va import", [$item]);
                        }
                        $order->price = $item->car_cost ?? null;
                        $order->utm_source = empty($item->utm_source) ? null : $item->utm_source;
                        $order->utm_medium = empty($item->utm_medium) ? null : $item->utm_medium;
                        $order->utm_term = empty($item->utm_term) ? null : $item->utm_term;
                        $order->utm_campaign = empty($item->utm_campaign) ? null : $item->utm_campaign;
                        $order->utm_content = empty($item->utm_content) ? null : $item->utm_content;
                        //$order->ip_address = $item->ip_address ?? null;
                        $order->referrer = $item->referrer ?? null;
                        $order->credit_period = ($item->credit_term === '-') ? null : $item->credit_term;
                        $order->initial_fee = empty($item->inital_pay) ? null : $item->inital_pay;
                        try {
                            $order->month_pay = empty($item->month_pay) ? null : preg_replace('/[^0-9]/', '', $item->month_pay);
                        } catch (Throwable $e) {
                            Log::error($e);
                        }

                        $order->car_year = empty($item->model_year) ? null : $item->model_year;
                        $order->work_experience = empty($item->work_term) ? null : $item->work_term;
                        $order->work_place = empty($item->work_org) ? null : $item->work_org;
                        $order->comment = $item->request_type;
                        $order->source_id = 51;
                        $order->save();
                        OrderCreated::dispatch($order);
                        } catch (Throwable $e) {
                            Log::error("ImportVaController error". $e);
                            //Log::emergency($item);
                        }

                    }

                }


            }
            Log::info('dilova end');

            //Log::info('IMPORTREQ imported successfully!' . date('d-M-Y H:i:s'));
        }



    }
}
