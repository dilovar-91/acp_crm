<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportRequestController extends Controller
{


    public function importByDate(Request $request)//: JsonResponse
    {
        $site = Site::where('id', $request->site_id)->whereNotNull('token')->first();

        if (!empty($site) > 0) {
            $client = new Client();
            $from = $request->from;
            //return $site;
            $params['form_params'] = array('token' => $site->token, 'last_request_date' => $from);
            $items = null;
            try {
                $response = $client->post($site->post_url, $params);
                //return $response;
                $items = json_decode($response->getBody());
            } catch (Throwable $e) {
                Log::emergency($e);
                Log::emergency($site);
            }


            //return  $items;

            if (is_array($items) && count($items) > 0) {
                /*if ($site->showroom_id == 9){
                    Log::emergency($items);
                }*/

                foreach ($items as $item) {
                    //try {
                    // Log::emergency($item);
                    //$mark = Brand::where('name', 'like', '%' . $item->brand . '%')->first();
                    $order = new Order;
                    if (!empty($item->last_name) || !empty($item->first_name) || !empty($item->middle_name)) {
                        $order->client_name = ((($item->last_name . ' ') ?? '') . (($item->first_name . ' ') ?? '') . (($item->middle_name . ' ') ?? ''));
                    } else {
                        $order->client_name = 'Новый клиент';
                    }

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
                        Log::error($e);
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
                    $order->source_id = 5;
                    $order->save();
                    OrderCreated::dispatch($order);
                    /*} catch (Throwable $e) {
                        Log::error($e);
                        //Log::emergency($item);
                    }*/

                }

            }
        }
    }

    public function import()//: JsonResponse
    {

        Log::info('dilo start');

        $lastLaunched = Cache::get('last_launched_at');
        if ($lastLaunched && $lastLaunched->diffInMinutes() < 6) {
            Log::info('request-vdl:import еще рано - '.$lastLaunched->diffInMinutes());
            return;
        }
        Cache::put('last_launched_at', now());
        Log::info('ImportRequestController imported successfully!');
        $sites = Site::whereIn('agency_id', [3])->where('active', 1)->whereNotNull('token')->with('request')->get();

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
                //$params['verify'] = false; // Disable SSL certificate verification] = array('token' => $site->token, 'last_request_date' => $last_request);
                 if ($site->id === 6558)  {
                     Log::info("last_request_time site_id: 6558 ". Carbon::now()->subSeconds(4)->format('Y-m-d H:i:s') . ' db last_req:' .$last_request );
                 }

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
                        Log::error("Error Response Body: $errorBody");
                    }

                    // Log the exception
                    Log::error("Request Exception: " . $e->getMessage());
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
                            Log::error("type error import", [$item]);
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
                            Log::error("ImportRequestController error". $e);
                            //Log::emergency($item);
                        }

                    }

                }


            }
            Log::info('dilo end');
            //return;

            //Log::info('IMPORTREQ imported successfully!' . date('d-M-Y H:i:s'));
        }



    }

    public function import_today()//: JsonResponse
    {
        //$sites = Site::whereIn('agency_id', [3, 6])->where('active',1)->get();

        //if(count($sites)>0) {
        // foreach ($sites as $site) {
        $client = new Client();
        $minut_ago = Carbon::now();
        $minut_ago->hour = 00;
        $minut_ago->minute = 00;
        $minut_ago->second = 00;;
        $minut_ago->format('Y-m-d H:i:s');
        Log::warning($minut_ago);
        //return $site;
        //Log::emergency($minut_ago);
        //$params['form_params'] = array('token' => $site->token, 'last_request_date' => $minut_ago);
        //$response = $client->post($site->post_url, $params);
        //return $response;
        $items = [
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 10:05:59",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Сергей",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9093760420",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "80445358",
                "utm_content" => "none.0",
                "utm_medium" => "cpc",
                "utm_source" => "yandexrsy",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 10:23:01",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Сергей",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9093760420",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "83074965",
                "utm_content" => "13468763619",
                "utm_medium" => "cpc",
                "utm_source" => "yandex",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 11:18:06",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Дмитрий",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9275389290",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82562749",
                "utm_content" => "none.0",
                "utm_medium" => "cpc",
                "utm_source" => "yandexrsy",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 11:32:30",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Алексей",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9047572827",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82434529",
                "utm_content" => "13366165676",
                "utm_medium" => "cpc",
                "utm_source" => "context",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 12:19:17",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "ТЕСТОВАЯ ЗАЯВКА",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9275949917",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82609167",
                "utm_content" => "13392815561",
                "utm_medium" => "cpc",
                "utm_source" => "context",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 14:11:23",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "ТЕСТОВАЯ ЗАЯВКА 2",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9372271030",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "83075117",
                "utm_content" => "13468783107",
                "utm_medium" => "cpc",
                "utm_source" => "yandex",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 14:14:00",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Светлана",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9876448286",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82434283",
                "utm_content" => "13366131800",
                "utm_medium" => "cpc",
                "utm_source" => "context",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 14:28:01",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "ТЕСТОВАЯ ЗАЯВКА 3",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9610850871",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82609167",
                "utm_content" => "13392815454",
                "utm_medium" => "cpc",
                "utm_source" => "context",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 17:14:20",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Олег",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9378240777",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82562844",
                "utm_content" => "none.0",
                "utm_medium" => "cpc",
                "utm_source" => "yandexrsy",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 17:28:53",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Олег",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9270781857",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "80445358",
                "utm_content" => "none.0",
                "utm_medium" => "cpc",
                "utm_source" => "yandexrsy",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 17:34:35",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Валерий",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9093410295",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "82609167",
                "utm_content" => "13392815553",
                "utm_medium" => "cpc",
                "utm_source" => "context",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 18:01:12",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "Данил",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9275452278",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "83075118",
                "utm_content" => "13468783184",
                "utm_medium" => "cpc",
                "utm_source" => "yandex",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ],
            [
                "ad_channel" => "",
                "age" => "",
                "brand" => "",
                "car_cost" => "",
                "created" => "2023-02-28 18:21:11",
                "credit_term" => "-",
                "desired_amount" => "",
                "email" => "",
                "first_name" => "",
                "inital_pay" => 0,
                "ip_address" => "",
                "last_name" => "тестовая заявка маска",
                "middle_name" => "",
                "mileage" => "",
                "mobile_tel" => "9047773292",
                "model" => "",
                "model_year" => "",
                "month_pay" => "",
                "org_tel" => "",
                "place_live" => "",
                "place_reg" => "",
                "referer" => "victory-crm",
                "request_type" => "",
                "utm_campaign" => "83075304",
                "utm_content" => "13468806376",
                "utm_medium" => "cpc",
                "utm_source" => "yandex",
                "work_income" => "",
                "work_org" => "",
                "work_term" => ""
            ]
        ];
        //return  $items;
        // Log::emergency($items);
        if (is_array($items) && count($items) > 0) {
            foreach ($items as $item) {

                //return $item['last_name'];
                try {
                    if ($item['brand'] !== "") {
                        $mark = Brand::where('name', 'like', '%' . $item['brand'] . '%')->first();
                    }
                    $order = new Order;
                    $order->client_name = ((($item['last_name'] . ' ') ?? '') . (($item['first_name'] . ' ') ?? '') . (($item['middle_name'] . ' ') ?? '')) ?? 'Новый клиент';
                    $order->showroom_id = 8;
                    $order->site_id = 1031;
                    $order->status_id = 1;
                    $order->phone = '+7' . $item['mobile_tel'];
                    $order->email = empty($item['email']) ? null : $item['email'];
                    $order->type_id = GeneralHelper::getType($item['request_type']);
                    $order->price = $item['car_cost'] ?? null;
                    $order->utm_source = empty($item['utm_source']) ? null : $item['utm_source'];
                    $order->utm_medium = empty($item['utm_medium']) ? null : $item['utm_medium'];
                    $order->utm_term = empty($item['utm_term']) ? null : $item['utm_term'];
                    $order->utm_campaign = empty($item['utm_campaign']) ? null : $item['utm_campaign'];
                    $order->utm_content = empty($item['utm_content']) ? null : $item['utm_content'];
                    //$order->ip_address = $item['ip_address'] ?? null;
                    $order->referrer = $item['referrer'] ?? null;

                    $order->month_pay = empty($item['month_pay']) ? null : $item['month_pay'];
                    $order->car_year = empty($item['model_year']) ? null : $item['model_year'];
                    $order->work_experience = empty($item['work_term']) ? null : $item['work_term'];
                    $order->work_place = empty($item['work_org']) ? null : $item['work_org'];
                    $order->comment = $item['request_type'];
                    $order->source_id = 5;
                    $order->save();
                    OrderCreated::dispatch($order);
                } catch (Throwable $e) {
                    Log::error($e);
                    //Log::emergency($item);
                }
                Log::emergency($item);
                //}

                //}
            }
            return response()->json(123);
        }

    }
}
