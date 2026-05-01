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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportVdlKrasnodar2Controller extends Controller
{

    private $elements;
    private $sites;


    public function __construct() {
        $this->elements = array(5 => 8, 8 => 5);
        $this->sites = array(5=>6559, 8=>6560 );
    }


    public function import()
    {
        $sites = Site::where('id', 6560)->whereNotNull('token')->with(['request'])->get();
        //return $sites;

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

                DB::table('last_request_time')->updateOrInsert(
                    ['site_id' => $site->id], // Add the site_id as the first argument
                    ['last_request_time' => now()]
                );
                $items = null;
                try {
                    $response = $client->get($site->post_url, $params);
                    //return $response;
                    $items = json_decode($response->getBody());
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

                    ///Log::emergency($items);

                    foreach ($items as $item) {

                        //try {
                        Log::error("vdl", [$item]);
                        //$mark = Brand::where('name', 'like', '%' . $item->brand . '%')->first();
                        $order = new Order;
                        $order->client_name = ((($item->last_name . ' ') ?? '') . (($item->first_name . ' ') ?? '') . (($item->middle_name . ' ') ?? '')) ?? 'Новый клиент';

                        //$last  = DB::table('last_queue')->where('site_id', $site->id)->first();
                        //$showroomIndex = $this->getNext($last->number);

                        $order->showroom_id = 5; //$showroomIndex;

                       /* DB::table('last_queue')
                            ->where('site_id', $site->id) // Assuming 'id' is the primary key
                            ->update(['number' => $showroomIndex, 'updated_at' => now()]);*/


                        $order->site_id = 6559;
                        // $order->site_id =  $this->sites[$showroomIndex];
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
                            Log::error("type error import vdl", [$item]);
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
                        $order->source_id = 17;
                        $order->save();
                        OrderCreated::dispatch($order);
                        /*} catch (Throwable $e) {
                            Log::error($e);
                            //Log::emergency($item);
                        }*/

                    }

                }


            }
            //return "Done";
        }

    }


    private function getNext($currentIndex): int
    {
        return $this->elements[$currentIndex];
    }
}
