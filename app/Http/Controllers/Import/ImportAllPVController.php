<?php

namespace App\Http\Controllers\Import;

use App\Events\OrderCreated;
use App\Events\OrderPvCreated;
use App\Helpers\GeneralHelper;
use App\Helpers\ShowroomHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\Site;
use App\Models\User;
use App\Services\DistributeService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportAllPVController extends Controller
{

    protected string $dayName;
    public function __construct()
    {
        $this->dayName = Carbon::now()->format('l');
    }
    public function import()
    {

        $distributeService = new DistributeService();
        $sites = Site::whereIn('id', [6608, 6038, 6351, 6452, 6716, 6717, 6822, 6823, 6824, 6825, 6838, 6839, 6840, 6841])->whereNotNull('token')->with(['request'])->get();
        if (count($sites) > 0) {
            foreach ($sites as $site) {
                if (isset($site->request->last_request_time)) {
                    $last_request = $site->request->last_request_time->addSeconds(1)->format('Y-m-d H:i:s');
                } else {
                    $last_request = Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s');
                }

                $client = new Client();
                $params['form_params'] = array('token' => $site->token, 'last_request_date' => $last_request);
                DB::table('last_request_time')->updateOrInsert(
                    ['site_id' => $site->id],
                    ['last_request_time' => now()]
                );
                $items = null;
                try {
                    $response = $client->get($site->post_url, $params);
                    $items = json_decode($response->getBody());
                } catch (RequestException $e) {
                    $errorResponse = $e->getResponse();
                    if ($errorResponse) {
                        $errorBody = $errorResponse->getBody()->getContents();
                        Log::error("Error Response Body: $errorBody");
                    }

                    // Log the exception
                    Log::error("Request Exception all pv: " . $e->getMessage());
                }

                //return  $items;


                if (is_array($items) && count($items) > 0) {
                    foreach ($items as $item) {
                        // Log::error("all pv", [$item]);
                        $order = new Order;
                        $order->client_name = ((($item->last_name . ' ') ?? '') . (($item->first_name . ' ') ?? '') . (($item->middle_name . ' ') ?? '')) ?? 'Новый клиент';

                        DB::table('last_queue')
                            ->where('site_id', $site->id)
                            ->update(['number' => $site->id, 'updated_at' => now()]);

                        $order->site_id = $site->id;
                        $order->status_id = 1;

                        $order->operator_id = $distributeService->distribute($site->showroom_id);
                        $order->showroom_id = ShowroomHelper::getShowroomPair($site->showroom_id);
                        $order->type_id = 21;

                        try {
                            $order->phone = PhoneNumber::make($item->mobile_tel, 'RU')->formatE164();
                        } catch (Throwable $e) {
                            $order->phone = preg_replace('/[^0-9+]/', '', $item->mobile_tel) ?? null;
                        }

                        $order->email = empty($item->email) ? null : $item->email;
                        try {
                            if ($item->brand !== '-' || $item->model !== '-') {
                                $car = GeneralHelper::getCar($item->brand, $item->model);
                                $order->mark_id = $car->brand_id ?? null;
                                $order->model_id = $car->id ?? null;
                            }
                        } catch (Throwable $e) {
                            Log::error("type error all pv vdl", [$item]);
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


                        $order->source_id = 102;

                        $order->save();
                        OrderPvCreated::dispatch($order);

                    }

                }

            }
        }
    }



}
