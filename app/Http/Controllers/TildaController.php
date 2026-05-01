<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\JustweModel;
use App\Models\LastQueue;
use App\Models\Order;
use App\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class TildaController extends Controller
{
    public function lead(Request $request)
    {
        Log::warning('Tilda');
        Log::warning($request);
        try {
            $order = new Order;
            $order->client_name = $request->Name ?? 'Новый клиент';
            $order->showroom_id = 5;
            $order->site_id = 6263;
            $order->status_id = 1;
            $order->type_id = 2;
            $car_name = null;
            $price = null;

            $phone = $request->Phone ?? $request->phone ?? null;
            if (isset($request->products)) {
                foreach ($request->products as $car) {
                    $car_name = $car->name . ' ';
                    $price = $car->price;
                }
            }
            try {
                $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = $phone;
            }

            if ($car_name !== null) {
                $order->comment = $car_name;
            }
            $order->price = $price;
            $order->referrer = $request->header('referer') ?? null;
            $order->entry_point = $request->header('referer') ?? null;
            $order->source_id = 10;
            $order->save();
            OrderCreated::dispatch($order);
        } catch (Throwable $exception) {
            Log::error($request);
            Log::error($exception);
        }

        return response()->json($order, 201);

    }

    public function create(Request $request, $showroom, $site)
    {
        //Log::warning('create Tilda');
        Log::info('create tilda', [
            'showroom_id' => $showroom,
            'site_id' => $site,
            'request' => $request->all(),
        ]);

        //Log::warning($showroom . ' <--showroom site--> ' . $site);
        try {
            $order = new Order;
            if ($request->filled('Name')) {
                $order->client_name = $request->name;
            } else {
                $order->client_name = 'Новый клиент';
            }
            $order->showroom_id = $showroom;
            $order->site_id = $site;
            $order->status_id = 1;
            $order->type_id = 2;
            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = $request->phone;
            }
            $comment = $request->comment ?? null;
            if ($request->filled('formname')) {
                $comment .= 'Форма: ' . $request->formname . "\n";
            }
            if ($request->filled('car')) {
                $comment .= 'Машина: ' . $request->car . "\n";
            }
            if ($request->filled('srok')) {
                $comment .= 'Срок: ' . $request->srok . "\n";
            }
            if ($request->filled('price')) {
                $comment .= 'Цена: ' . $request->price . "\n";
            }
            $comment = trim($comment);
            if (!empty($comment)) {
                $order->comment = $comment;
            }
            $order->save();
            OrderCreated::dispatch($order);
        } catch (Throwable $exception) {
            Log::error($request);
            Log::error($exception);
        }

        return response()->json($order, 201);

    }


    public function createLead(Request $request, $showroom_id, $site_id)
    {
        //Log::info($request->all());

        Log::info('CreateLead tilda', [
            'showroom_id' => $showroom_id,
            'site_id' => $site_id,
            'request' => $request->all(),
        ]);

        if ($request->input('test') === 'test') {
            return response()->json(['status' => 'ok'], 201);
        }

        try {
            $rawFields = $request->input('data.attributes.fields');

            if ($rawFields && is_array($rawFields)) {
                $fields = $rawFields;
            } else {
                $fields = [
                    'phone' => $request->input('phone') ?? $request->input('Phone'),
                    'subject' => $request->input('formname'),
                    'tranid' => $request->input('tranid'),
                    'formid' => $request->input('formid'),
                    'analytics_page_url' => $request->input('page') ?? '',
                    'analytics_page_title' => $request->input('formname'),
                ];
            }

            $validated = validator([
                'site_id' => $site_id,
                'showroom_id' => $showroom_id,
                'phone' => $fields['phone'] ?? null,
            ], [
                'site_id' => 'required',
                'showroom_id' => 'required|integer',
                'phone' => 'required|string',
            ])->validate();

            $site = Site::find($site_id);
            $siteUrl = $site ? $site->url . ($fields['analytics_page_url'] ?? '') : null;

            $commentFields = [
                'Название страницы' => $fields['analytics_page_title'] ?? null,
                'Пробег' => $fields['mileage'] ?? null,
                'Модель' => $fields['model_name'] ?? null,
                'Тема' => $fields['subject'] ?? null,
                'Город' => $fields['city'] ?? null,
                'Трансмиссия' => $fields['transmission'] ?? null,
                'Телефон' => $fields['phone'] ?? null,
                'Город дилера' => $fields['dealer_city'] ?? null,
                'Пол' => $fields['gender'] ?? null,
                'Дилер' => $fields['dealer_name'] ?? null,
                'VIN' => $fields['vin'] ?? null,
                //'Год' => $fields['year'] ?? null,
                'Тип' => $fields['customer_type'] ?? null,
                'TranID' => $fields['tranid'] ?? null,
                'FormID' => $fields['formid'] ?? null,
            ];

            $filtered = array_filter($commentFields, fn($v) => !empty($v));
            $commentText = collect($filtered)->map(fn($v, $k) => "$k: $v")->implode("\n");

            $clientNameParts = array_filter([
                $fields['last_name'] ?? null,
                $fields['name'] ?? null,
                $fields['second_name'] ?? null,
            ]);

            $clientName = implode(' ', $clientNameParts) ?: 'Новый клиент';

            $order = new Order();
            $order->entry_point = $siteUrl;
            $order->client_name = $clientName;
            $order->showroom_id = $validated['showroom_id'];

            try {
                $order->phone = PhoneNumber::make($fields['phone'], 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $fields['phone']);
            }

            $order->site_id = $validated['site_id'];
            $order->status_id = 1;
            $order->comment = $commentText;
            $order->utm_source = $fields['analytics_page_utm_source'] ?? null;
            $order->utm_term = $fields['analytics_page_utm_term'] ?? null;
            $order->utm_campaign = $fields['analytics_page_utm_campaign'] ?? null;
           //$order->car_year = (int) ($fields['year'] ?? 0);

            $order->save();

            OrderCreated::dispatch($order);

            return response()->json([
                'message' => 'Lead created successfully',
                'order_id' => $order->id
            ], 201);

        } catch (Throwable $exception) {
            Log::error('Lead Creation Error', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create lead',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }


    public function createDistribute(Request $request, $showroom_id, $site_id)
    {
        Log::info('createDistribute tilda', [
            'showroom_id' => $showroom_id,
            'site_id' => $site_id,
            'request' => $request->all(),
        ]);


        if ($request->input('test') === 'test') {
            return response()->json(['status' => 'ok'], 201);
        }

        try {
            $rawFields = $request->input('data.attributes.fields');

            if ($rawFields && is_array($rawFields)) {
                $fields = $rawFields;
            } else {
                $fields = [
                    'phone' => $request->input('phone') ?? $request->input('Phone'),
                    'subject' => $request->input('formname'),
                    'tranid' => $request->input('tranid'),
                    'formid' => $request->input('formid'),
                    'analytics_page_url' => $request->input('page') ?? '',
                    'analytics_page_title' => $request->input('formname'),
                ];
            }

            $validated = validator([
                'site_id' => $site_id,
                'showroom_id' => $showroom_id,
                'phone' => $fields['phone'] ?? null,
            ], [
                'site_id' => 'required',
                'showroom_id' => 'required|integer',
                'phone' => 'required|string',
            ])->validate();

            $site = Site::find($site_id);
            $siteUrl = $site ? $site->url . ($fields['analytics_page_url'] ?? '') : null;

            $commentFields = [
                'Название страницы' => $fields['analytics_page_title'] ?? null,
                'Пробег' => $fields['mileage'] ?? null,
                'Модель' => $fields['model_name'] ?? null,
                'Тема' => $fields['subject'] ?? null,
                'Город' => $fields['city'] ?? null,
                'Трансмиссия' => $fields['transmission'] ?? null,
                'Телефон' => $fields['phone'] ?? null,
                'Город дилера' => $fields['dealer_city'] ?? null,
                'Пол' => $fields['gender'] ?? null,
                'Дилер' => $fields['dealer_name'] ?? null,
                'VIN' => $fields['vin'] ?? null,
                'Год' => (int) ($fields['year'] ?? 0),
                'Тип' => $fields['customer_type'] ?? null,
                'TranID' => $fields['tranid'] ?? null,
                'FormID' => $fields['formid'] ?? null,
            ];

            $filtered = array_filter($commentFields, fn($v) => !empty($v));
            $commentText = collect($filtered)->map(fn($v, $k) => "$k: $v")->implode("\n");

            $clientNameParts = array_filter([
                $fields['last_name'] ?? null,
                $fields['name'] ?? null,
                $fields['second_name'] ?? null,
            ]);

            $clientName = implode(' ', $clientNameParts) ?: 'Новый клиент';



            $order = new Order();
            $order->entry_point = $siteUrl;
            $order->client_name = $clientName;

            $showroom_id = $validated['showroom_id'];

            $group = GeneralHelper::getShowroomGroup($showroom_id);

            // dd($group);

            $showrooms = [];

            if ($group === 'rostov') {
                $showrooms = [2 => 4, 4 => 2];
            } elseif ($group === 'krasnodar') {
                $showrooms = [5 => 8, 8 => 5];
            } elseif ($group === 'simferopol') {
                $showrooms = [15 => 17, 17 => 15];
            }

            //dd($showrooms);


            $last = LastQueue::firstOrCreate(
                [
                    'site_id' => $site_id,
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
                $showroomIndex = $showrooms[$last->number] ??  $showroom_id;
            }


            LastQueue::where('site_id', $last->site_id)->update([
                'number' => $showroomIndex,
                'percent' => $percent,
                'updated_at' => now(),
            ]);
            $order->showroom_id = $showroomIndex;
            $order->site_id = $last->site_id;
            $order->status_id = 1;

            try {
                $order->phone = PhoneNumber::make($fields['phone'], 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $fields['phone']);
            }

            //$order->site_id = $validated['site_id'];
            $order->comment = $commentText;
            $order->utm_source = $fields['analytics_page_utm_source'] ?? null;
            $order->utm_term = $fields['analytics_page_utm_term'] ?? null;
            $order->utm_campaign = $fields['analytics_page_utm_campaign'] ?? null;
            // $order->car_year = (int) ($fields['year'] ?? 0);

            $order->save();

            OrderCreated::dispatch($order);

            return response()->json([
                'message' => 'Lead created successfully',
                'order' => $order
            ], 201);

        } catch (Throwable $exception) {
            Log::error('Lead Creation Error', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create lead',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

}
