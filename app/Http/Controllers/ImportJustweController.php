<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\ShowroomHelper;
use App\Models\Order;
use App\Models\Site;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportJustweController extends Controller
{
    public function import()
    {
        $sites = Site::whereIn('agency_id', [11, 13])->where('active', 1)->whereNotNull('token')->with(['request'])->get();
        if (count($sites) > 0) {
            foreach ($sites as $site) {
                if (isset($site->request->last_request_time)) {
                    //Log::alert('last_request_time');
                    $last_request = $site->request->last_request_time->format('Y-m-d H:i:s');

                } else {
                    $last_request = Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s');
                }

                $response = null;


                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $site->token,
                    ])->get($site->post_url . '?from=' . $last_request . '&limit=5000');
                    DB::table('last_request_time')->updateOrInsert(
                        ['site_id' => $site->id], // Add the site_id as the first argument
                        ['last_request_time' => now()]
                    );
                } catch (Throwable $e) {
                    Log::emergency($e);
                    Log::error("justwe agency error", [$site]);
                    continue;
                }


                try {
                    $data = $response->json();

                    if (isset($data['data']['items']) && is_array($data['data']['items'])) {

                        foreach ($data['data']['items'] as $item) {

                            try {


                                // Save each item to the database
                                $clientName = trim(implode(' ', array_filter([$item['clientFirstName'], $item['clientMiddleName'], $item['clientLastName']])));

                                try {
                                    $phone = PhoneNumber::make($item['clientPhoneNumber'], 'RU')->formatE164();
                                } catch (Throwable $e) {
                                    $phone = preg_replace('/[^0-9+]/', '', $item['clientPhoneNumber']) ?? null;
                                }

                                $order = new Order();
                                $order->client_name = $clientName ?? " ";
                                $order->phone = $phone;
                                $order->utm_campaign = $item['utmCampaign'] ?? null;
                                $order->utm_content = $item['utmContent'] ?? null;
                                $order->utm_medium = $item['utmMedium'] ?? null;
                                $order->utm_source = $item['utmSource'] ?? null;
                                $order->utm_term = $item['utmTerm'] ?? null;
                                $order->site_id = $site->id;
                                $order->source_id = 222;
                                $order->showroom_id = ShowroomHelper::getShowroomPair($site->showroom_id); // justwe
                                $order->status_id = 1;
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


    /*public function handle()
    {

        $orders = Order::where('showroom_id', 9)->whereNull('operator_id')->where('site_id', 6454)->get();
        //return $orders;
        foreach ($orders as $order) {

                $phone = $order->phone;
                $res = Order::search($phone)->where('showroom_id', $order->showroom_id)->get();
                $order2 = Order::where('id', $order->id)->with('retries')->first();


                $blacklist = Cache::remember('blacklist_' . $phone, now()->addMinutes(800), function () use ($phone) {
                    return Blacklist::search($phone)->first();
                });

                if ($blacklist && !empty($order2)) {
                    $order2->form_id = $order->showroom_id;
                    $order2->showroom_id = 16;
                    $order2->comment = "ЧС";
                    $order2->save();
                    return;
                }

                //Log::emergency("count " . count($res));
                if (count($res) > 1) {
                    if (!empty($order2) && ($order->operator_id === null || $order->operator_id === 1000)) {
                        $owner = Order::search($phone)->where('id', '<>', $order->id)->where('showroom_id', $order->showroom_id)->first();
                        if (!empty($owner) && $owner->operator_id !== null && $this->checkUserSchedule($owner->operator_id) === true) {
                            if ($this->checkUserSchedule($owner->operator_id)) {
                                $order2->operator_id = $owner->operator_id;
                            } else {
                                $order2->operator_id = $this->distribute($order->showroom_id);
                            }

                        } else if ($order2->operator_id === null) {
                            $order2->operator_id = $this->distribute($order->showroom_id);
                        }
                    }
                    if (count($res) > 1) {
                        $order2->retries = count($res) - 1;
                        if ($order2->showroom_id === 9) {
                            $order2->status_id = 8;
                            $order2->payment_method = 6;
                        }
                    }
                    //Log::emergency("first " . $order->operator_id);

                } else if (!empty($order2) && ($order2->operator_id === null || $order2->operator_id === 1000)) {
                    $order2->operator_id = $this->distribute($order->showroom_id);

                }
                $order2->phone = $phone;
                $order2->save();


        }


    }

    public function distribute($showroom_id)
    {
        $from = null;
        $to = null;

        $ot = Carbon::now();
        $do = Carbon::now();
        if ($showroom_id === 9){
            $ot->hour = 18;
            $ot->minute = 30;
        } else {
            $ot->hour = 19;
            $ot->minute = 00;
        }
        $ot->second = 00;
        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $from = Carbon::now();
            $to = Carbon::now()->addDays(1);
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
            $from = Carbon::now()->subDays(1);
            $to = Carbon::now();
        }

        if ($showroom_id === 9){
            $from->hour = 18;
            $from->minute = 30;
            $to->hour = 18;
            $to->minute = 30;
        } else {
            $from->hour = 19;
            $from->minute = 00;
            $to->hour = 19;
            $to->minute = 00;
        }
        $from->second = 00;
        $to->second = 00;


        $res = User::with(['schedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])

            ->whereIn('role_id', [2,6])
            ->where('showroom_id', $showroom_id)
            ->whereHas('schedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->withCount([
                'orders' => function ($query) use ($from, $to) {
                    //$query->whereBetween('created_at', [$from, $to])->whereNull('retries');
                    $query->leftJoin('sites', 'orders.site_id', '=', 'sites.id')
                        ->where(function ($subquery) {
                            $subquery->where('sites.type_id', '<>', 4)
                                ->orWhereNull('sites.type_id');
                        })
                        ->where(function ($subquery) {
                            $subquery->whereNull('orders.site_id')
                                ->orWhereNotNull('orders.site_id'); // Include orders with site_id = NULL
                        })
                        ->whereBetween('orders.created_at', [$from, $to])
                        ->whereNull('orders.retries');
                }
            ])
            ->orderBy('orders_count')
            ->first();
        return $res->id ?? null;
    }



    public function checkUserSchedule($user_id)
    {
        $ot = Carbon::now();
        $do = Carbon::now();


        $ot->hour = 19;
        $ot->minute = 00;
        $ot->second = 00;

        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
        }

        $user = User::where('id', $user_id)->whereHas('schedule', function ($query) use ($today) {
            $query->where($today, 1);
        })
            ->first();
        return !empty($user);

    }*/
}
