<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Order;
use App\Models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class ImportJustweVdlController extends Controller
{
    private $elements;
    private $sites;


    public function __construct() {
        $this->elements = array(2 => 4, 4 => 14, 14 => 2);
        $this->sites = array(2=>6517, 4=>6518 , 14=>6519 );
    }


    public function import()
    {
        $sites = Site::where('id', 6516)->whereNotNull('token')->with(['request'])->get();

        //return $sites;

        if (count($sites) > 0) {
            foreach ($sites as $site) {
                //return $site->request->last_request_time->addSeconds(2);
                if (isset($site->request->last_request_time)) {
                    $last_request = $site->request->last_request_time->addSeconds(1)->format('Y-m-d H:i:s');
                } else {
                    $last_request = Carbon::now()->subMinutes(1)->format('Y-m-d H:i:s');
                }

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

                                $last = DB::table('last_queue')->where('site_id', $site->id)->first();

                                $showroomIndex = $this->getNext($last->number);

                                $order->showroom_id = 14; //$showroomIndex;
                                DB::table('last_queue')
                                    ->where('site_id', $site->id)
                                    ->update(['number' => $showroomIndex, 'updated_at' => now()]);


                                $order->site_id = 6519;//$this->sites[$showroomIndex];
                                $order->source_id = 18; // justwe
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
            return "Done";
        }
    }


    private function getNext($currentIndex): int
    {
        return $this->elements[$currentIndex];
    }
}
