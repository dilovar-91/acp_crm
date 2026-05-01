<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\LastQueue;
use App\Models\Order;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class TargetController extends Controller
{
    public function leadTarget(Request $request)
    {
        try {
            $order = new Order;

            // Basic Info
            $order->client_name = trim(($request->get('name') ?? '') . ' ' . ($request->get('lastname') ?? '')) ?: 'Новый клиент';

            $order->status_id = 1;
            $order->type_id = 1;
            //$order->site_id = $request->get('site_id') ?? null;
            //$order->showroom_id = $request->get('showroom_id') ?? null;

            // $site = Site::where('id', $request->get('site_id'))->firstOrFail();


            //$showrooms = array(10 => 14, 14 => 10);


            $showroom_id = $request->get('showroom_id');

            $group = GeneralHelper::getShowroomGroup($showroom_id);

            $showrooms = [];
            if ($group === 'krasnodar') {
                $order->showroom_id = $request->get('showroom_id');
                $order->site_id = $request->get('site_id');
            } else {
                if ($group === 'rostov') {
                    $showrooms = [10 => 10];
                } elseif ($group === 'krasnodar') {
                    $showrooms = [5 => 8, 8 => 5];
                } elseif ($group === 'simferopol') {
                    //$showrooms = [15 => 17, 17 => 15];
                    $showrooms = [15 => 15]; // временно отключаем С2
                }

                //dd($showrooms);


                $last = LastQueue::firstOrCreate(
                    [
                        'site_id' => $request->get('site_id'),
                    ],
                    [
                        'number' => $showroom_id,
                        'percent' => 50,
                    ]
                );

                $percent = $last->percent ?? 0;

                if ($last->number === $showroom_id && $percent < 50) {
                    $percent += 50;
                    $showroomIndex = $showroom_id;
                } else {
                    $percent = 50;
                    $showroomIndex = $showrooms[$last->number] ?? $showroom_id;
                }


                LastQueue::where('site_id', $last->site_id)->update([
                    'number' => $showroomIndex,
                    'percent' => $percent,
                    'updated_at' => now(),
                ]);


                $order->showroom_id = $showroomIndex;


                $order->site_id = $last->site_id;

            }




            try {
                $order->phone = PhoneNumber::make($request->get('phone'), 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->get('phone')) ?? null;
            }

            $order->complectation = $request->get('equipment') ?? null;

            $order->price = $request->get('price') ?? null;
            $order->referrer = $request->get('referrer') ?? null;
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = 603;

            $commentParts = [
                'Марка: ' . ($request->get('MARK') ?? '-'),
                'Модель: ' . ($request->get('MODEL') ?? '-'),
                'Дата: ' . ($request->get('date') ?? '-'),
                'Дата рождения: ' . ($request->get('bday') ?? '-'),
                'Номер получателя: ' . ($request->get('tonumber') ?? '-'),
                'Comagic ID: ' . ($request->get('comagicid') ?? '-'),
                'Взнос: ' . ($request->get('vznos') ?? '-'),
                'Комплектация: ' . ($request->get('equipment') ?? '-'),
                'Кредит: ' . ($request->get('credit_term') ?? '-'),
                'Взнос: ' . ($request->get('vznos') ?? '-'),
                'Сообщение: ' . ($request->get('message') ?? '-'),
            ];

            if ($request->get('promocod')) {
                $commentParts[] = 'Промокод: ' . $request->get('promocod');
            }

            $order->comment = trim(implode(' | ', array_filter($commentParts)));

            // UTM tags
            $order->utm_source = $request->get('utm_source') ?? null;
            $order->utm_medium = $request->get('utm_medium') ?? null;
            $order->utm_term = $request->get('utm_term') ?? null;
            $order->utm_campaign = $request->get('utm_campaign') ?? null;
            $order->utm_content = $request->get('utm_content') ?? null;
            $order->platform = $request->get('platform') ?? null;
            $order->device = $request->get('device') ?? null;
            $order->source_type = $request->get('source_type') ?? null;
            $order->ads_site = $request->get('site') ?? null;

            $order->save();

            OrderCreated::dispatch($order);

            return response()->json($order, 201);
        } catch (Throwable $exception) {
            Log::error('Lead Target error', [
                'request' => $request->all(),
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['err' => $exception->getMessage(), 'error' => 'Failed to create order on Target'], 500);
        }
    }
}
