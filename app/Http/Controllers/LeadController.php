<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\CarModel;
use App\Models\JustweModel;
use App\Models\LastQueue;
use App\Models\Order;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class LeadController extends Controller
{


    private $showrooms;
    private $sites;


    public function __construct()
    {
        $this->showrooms = array(2 => 4, 4 => 10, 10 => 2);
        $this->sites = array(2 => 7278, 4 => 7277, 10 => 7467);
    }

    public function lead(Request $request, $id)
    {
        try {
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';
            $order->showroom_id = $id;
            $order->site_id = $request->site_id ?? null;
            $order->status_id = 1;
            $order->type_id = GeneralHelper::getType($request->type);
            $order->region_id = $request->region_id ?? null;

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }

            $get_model = JustweModel::where('id', $request->model_id)->first();
            $order->mark_id = $get_model->brandId ?? null;
            $order->model_id = $get_model->modelId ?? null;
            if ($request->mark_id === null || $request->model_id === null) {
                $order->comment = ($request->mark_name ?? '') . ' ' . ($request->model_name ?? '');
            }
            if (!empty($request->comment)) {
                $order->comment = $order->comment . ' ' . $request->comment;
            }

            $order->price = $request->price ?? null;
            $order->utm_source = $request->utm_source ?? null;
            $order->utm_medium = $request->utm_medium ?? null;
            $order->utm_term = $request->utm_term ?? null;
            $order->utm_campaign = $request->utm_campaign ?? null;
            $order->utm_content = $request->utm_content ?? null;
            //$order->ip_address = $request->ip ?? null;
            $order->form_id = $request->form_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->complectation = $request->complectation ?? null;
            $order->entry_point = $request->entry_point ?? null;
            $order->source_id = 2;
            $order->save();
            OrderCreated::dispatch($order);
        } catch (Throwable $exception) {
            Log::error($request);
            Log::error($exception);
        }

        return response()->json($order, 201);

    }


    public function leadDrive(Request $request)
    {
        try {
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';

            $order->status_id = 1;
            $order->type_id = GeneralHelper::getType($request->type);
            $order->region_id = $request->region_id ?? null;


            $showrooms = array(2 => 4, 4 => 10, 10 => 2);
            $sites = array(2 => 7300, 4 => 7301, 10 => 7466);


            $last = DB::table('last_queue')->where('site_id', 7299)->first();

            $percent = $last->percent ?? 0;
            //$showroomIndex = $this->getNext();
            if ($last->number === 2 && $percent < 20) {
                $percent += 20;
                $showroomIndex = 2;
            } else {
                $percent = 20;
                $showroomIndex = GeneralHelper::getNextShowroomId($last->number, $showrooms);
            }

            DB::table('last_queue')
                ->where('site_id', 7299)
                ->update(['number' => $showroomIndex, 'percent' => $percent, 'updated_at' => now()]);


            $order->showroom_id = $showroomIndex;

            $order->site_id = $sites[$showroomIndex];


            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }

            $get_model = JustweModel::where('id', $request->model_id)->first();
            $order->mark_id = $get_model->brandId ?? null;
            $order->model_id = $get_model->modelId ?? null;
            if ($request->mark_id === null || $request->model_id === null) {
                $order->comment = ($request->mark_name ?? '') . ' ' . ($request->model_name ?? '');
            }
            if (!empty($request->comment)) {
                $order->comment = $order->comment . ' ' . $request->comment;
            }

            $order->price = $request->price ?? null;
            $order->utm_source = $request->utm_source ?? null;
            $order->utm_medium = $request->utm_medium ?? null;
            $order->utm_term = $request->utm_term ?? null;
            $order->utm_campaign = $request->utm_campaign ?? null;
            $order->utm_content = $request->utm_content ?? null;
            //$order->ip_address = $request->ip ?? null;
            $order->form_id = $request->form_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->complectation = $request->complectation ?? null;
            $order->entry_point = $request->entry_point ?? null;
            $order->source_id = 2;
            $order->save();
            OrderCreated::dispatch($order);
            return response()->json(['message' => 'AutoDrive created', 'order_id' => $order->id], 201);

        } catch (Throwable $exception) {
            Log::error('AutoDrive Error', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to create lead',
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function create(Request $request)
    {
        try {
            $comment = null;
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';
            $order->showroom_id = $request->showroom_id;
            $order->site_id = $request->site_id ?? null;
            $order->status_id = 1;
            $order->type_id = GeneralHelper::getType($request->type);
            $order->region_id = $request->region_id ?? null;
            // $order->phone = $request->phone;

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }

            $get_model = JustweModel::where('id', $request->model_id)->first();
            $order->mark_id = $get_model->brandId ?? null;
            $order->model_id = $get_model->modelId ?? null;
            if ($request->mark_id === null || $request->model_id === null) {
                $comment = ($request->mark_name ?? '') . ' ' . ($request->model_name ?? '');
            }
            $order->price = $request->price ?? null;
            $order->utm_source = $request->utm_source ?? null;
            $order->utm_medium = $request->utm_medium ?? null;
            $order->utm_term = $request->utm_term ?? null;
            $order->utm_campaign = $request->utm_campaign ?? null;
            $order->utm_content = $request->utm_content ?? null;
            //$order->ip_address = $request->ip ?? null;
            $order->form_id = $request->form_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->complectation = $request->complectation ?? null;
            $order->entry_point = $request->entry_point ?? null;
            $order->source_id = 7;
            $order->comment = $request->comment . "" . $comment;
            $order->save();

            OrderCreated::dispatch($order);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error($request);
            report($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function createAutospot(Request $request, $showroom_id)
    {
        try {
            $comment = null;
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';
            $order->showroom_id = $showroom_id;
            $order->site_id = $request->site_id ?? null;
            $order->status_id = 1;
            $order->type_id = GeneralHelper::getType($request->type);
            $order->region_id = $request->region_id ?? null;
            // $order->phone = $request->phone;

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }

            $get_model = JustweModel::where('id', $request->model_id)->first();
            $order->mark_id = $get_model->brandId ?? null;
            $order->model_id = $get_model->modelId ?? null;
            if ($request->mark_id === null || $request->model_id === null) {
                $comment = ($request->mark_name ?? '') . ' ' . ($request->model_name ?? '');
            }
            $order->price = $request->price ?? null;
            $order->utm_source = $request->utm_source ?? null;
            $order->utm_medium = $request->utm_medium ?? null;
            $order->utm_term = $request->utm_term ?? null;
            $order->utm_campaign = $request->utm_campaign ?? null;
            $order->utm_content = $request->utm_content ?? null;
            //$order->ip_address = $request->ip ?? null;
            $order->form_id = $request->form_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->complectation = $request->complectation ?? null;
            $order->entry_point = $request->entry_point ?? null;
            $order->source_id = 311;
            $order->comment = $request->comment . "" . $comment;
            $order->save();

            OrderCreated::dispatch($order);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error($request);
            report($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function pairDistribute(Request $request)
    {


        Log::warning('pairDistribute req', [
            'request' => $request->all(),
        ]);

        try {
            $comment = null;
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';

            $showroom_id = $request->get('showroom_id');

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


            $order->status_id = 1;
            $order->type_id = GeneralHelper::getType($request->type);
            $order->region_id = $request->region_id ?? null;
            // $order->phone = $request->phone;

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }

            $get_model = JustweModel::where('id', $request->model_id)->first();
            $order->mark_id = $get_model->brandId ?? null;
            $order->model_id = $get_model->modelId ?? null;
            if ($request->mark_id === null || $request->model_id === null) {
                $comment = ($request->mark_name ?? '') . ' ' . ($request->model_name ?? '');
            }
            $order->price = $request->price ?? null;
            $order->utm_source = $request->utm_source ?? null;
            $order->utm_medium = $request->utm_medium ?? null;
            $order->utm_term = $request->utm_term ?? null;
            $order->utm_campaign = $request->utm_campaign ?? null;
            $order->utm_content = $request->utm_content ?? null;
            //$order->ip_address = $request->ip ?? null;
            $order->form_id = $request->form_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->complectation = $request->complectation ?? null;
            $order->entry_point = $request->entry_point ?? null;
            $order->source_id = 212;
            $order->comment = $request->comment . "" . $comment;
            $order->save();

            OrderCreated::dispatch($order);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error('Lead Target error', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function createVictory(Request $request)
    {
        try {

            $site_id = $this->getSite($request->api_key);
            $order = new Order;
            $order->client_name = $request->client_name ?? 'Новый клиент';
            $order->showroom_id = $this->getShowroom($request->api_key);
            $order->site_id = $site_id;
            $order->status_id = 1;
            $order->type_id = 22;

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
            }
            $order->utm_source = $request->domain ?? null;
            $order->source_id = 65;
            $order->comment = $request->comment ?? null;
            $order->save();
            OrderCreated::dispatch($order);
            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error(['req' => $request, 'err' => $e->getMessage()]);
            // report($e);
        }

    }

    public function getShowroom($token)
    {
        $res = Site::where('token', $token)->first();
        return $res->showroom_id ?? 6;
    }

    public function getSite($token)
    {
        $res = Site::where('token', $token)->first();
        return $res->id ?? 6;
    }


    public function leadFromVkAds(Request $request, $showroom_id)
    {
        try {
            $order = new Order;
            $order->client_name = $request->get('first_name', 'Новый клиент') . ' ' . $request->get('last_name', '');
            $order->showroom_id = $showroom_id;
            $order->site_id = $request->get('site_id') ?? null;
            $order->status_id = 1; // Статус "новый"
            $order->type_id = 1;
            // $order->region_id = $request->get('region_id') ?? null;

            // Обработка телефона
            $phone = $request->get('phone');
            if ($phone) {
                try {
                    $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone = preg_replace('/[^0-9+]/', '', $phone) ?: null;
                }
            }


            // Цена и UTM-метки (часто приходят из Albato как часть данных)
            $order->price = $request->get('price') ?? null;
            $order->utm_source = $request->get('utm_source') ?? 'vk';
            $order->type_id = $request->get('type_id') ?? 23;
            $order->utm_medium = $request->get('utm_medium') ?? 'cpc';
            $order->utm_campaign = $request->get('utm_campaign') ?? null;
            $order->utm_term = $request->get('utm_term') ?? null;
            $order->utm_content = $request->get('utm_content') ?? null;
            $order->comment = $request->get('comment') ?? null;

            // Дополнительные поля Albato / VK Ads

            $order->referrer = $request->get('referrer') ?? 'https://vk.com/ads';
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = $request->get('source_id') ?? 117;;

            // Сохраняем заказ
            $order->save();

            // Запускаем событие
            OrderCreated::dispatch($order);


            Log::error('VK Ads Request', [
                'request' => $request->all()
            ]);

            return response()->json(['message' => 'Lead created', 'order_id' => $order->id], 201);

        } catch (Throwable $exception) {
            Log::error('VK Ads Lead Error', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to create lead',
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function leadVk(Request $request)
    {
        try {

            Log::info($request);
            $order = new Order;
            $order->client_name = $request->get('first_name', 'Новый клиент') . ' ' . $request->get('last_name', '');

            //$site_id = $request->get('site_id');
            /*
            $last = DB::table('last_queue')->where('site_id', $site_id)->first();

            $percent = $last->percent ?? 0;
            //$showroomIndex = $this->getNext();
            if ($last->number === 2 && $percent < 20) {
                $percent += 20;
                $showroomIndex = 2;
            } else {
                $percent = 20;
                $showroomIndex = $this->getNext($last->number);
            }

            DB::table('last_queue')
                ->where('site_id', $site_id) // Assuming 'id' is the primary key
                ->update(['number' => $showroomIndex, 'percent' => $percent, 'updated_at' => now()]);
            */

            $order->showroom_id = $request->get('showroom_id');;

            $order->site_id = $request->get('site_id') ?? null;;


            $order->status_id = 1; // Статус "новый"
            $order->type_id = 1;
            // $order->region_id = $request->get('region_id') ?? null;

            // Обработка телефона
            $phone = $request->get('phone');
            if ($phone) {
                try {
                    $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone = preg_replace('/[^0-9+]/', '', $phone) ?: null;
                }
            }


            // Цена и UTM-метки (часто приходят из Albato как часть данных)
            $order->price = $request->get('price') ?? null;
            $order->utm_source = $request->get('utm_source') ?? 'vk';
            $order->type_id = $request->get('type_id') ?? 23;
            $order->utm_medium = $request->get('utm_medium') ?? 'cpc';
            $order->utm_campaign = $request->get('utm_campaign') ?? null;
            $order->utm_term = $request->get('utm_term') ?? null;
            $order->utm_content = $request->get('utm_content') ?? null;

            // Дополнительные поля Albato / VK Ads

            $order->referrer = $request->get('referrer') ?? 'https://vk.com/ads';
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = $request->get('source_id') ?? 131;;
            $order->comment = $request->get('comment') ?? null;;

            // Сохраняем заказ
            $order->save();

            // Запускаем событие
            OrderCreated::dispatch($order);

            return response()->json(['message' => 'Lead created', 'order_id' => $order->id], 201);

        } catch (Throwable $exception) {
            Log::error('VK Ads Lead Error', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to create lead',
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function leadTarget(Request $request)
    {
        try {
            $order = new Order;

            // Basic Info
            $order->client_name = trim(($request->get('name') ?? '') . ' ' . ($request->get('lastname') ?? '')) ?: 'Новый клиент';

            $order->status_id = 1;
            $order->type_id = 1;
            $order->site_id = $request->get('site_id') ?? null;
            $order->showroom_id = $request->get('showroom_id') ?? null;


            // Phone formatting
            try {
                $order->phone = PhoneNumber::make($request->get('phone'), 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = preg_replace('/[^0-9+]/', '', $request->get('phone')) ?? null;
            }

            $order->complectation = $request->get('equipment') ?? null;
            //$order->credit_term = $request->get('credit_term') ?? null;

            // Comment and meta data
            $order->price = $request->get('price') ?? null;
            $order->referrer = $request->get('referrer') ?? null;
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = 603;

            $commentParts = [
                $request->get('message'),
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
            Log::error('LeadTarget VK error', [
                'request' => $request->all(),
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to create order VK rasp'], 500);
        }
    }


    public function vkDistributeRostov(Request $request)
    {
        try {

            //Log::info($request);
            $order = new Order;
            $order->client_name = $request->get('first_name', 'Новый клиент') . ' ' . $request->get('last_name', '');

            $site_id = $request->get('site_id');

            $last = DB::table('last_queue')->where('site_id', $site_id)->first();

            $percent = $last->percent ?? 0;
            //$showroomIndex = $this->getNext();


            $showrooms = array(4 => 2, 2 => 10, 10 => 4);
            $sites = array(4 => 7123, 2 => 7595, 10 => 7596);


            if ($last->number === 4 && $percent < 30) {
                $percent += 30;
                $showroomIndex = 4;
            } else {
                $percent = 30;
                $showroomIndex = $this->getNextShowroomId($showrooms, $last->number);
            }

            DB::table('last_queue')
                ->where('site_id', $site_id) // Assuming 'id' is the primary key
                ->update(['number' => $showroomIndex, 'percent' => $percent, 'updated_at' => now()]);


            //$order->showroom_id = $request->get('showroom_id');;

            //$order->site_id = $request->get('site_id') ?? null;;

            $order->showroom_id = $showroomIndex;

            $order->site_id = $sites[$showroomIndex];


            $order->status_id = 1; // Статус "новый"
            $order->type_id = 1;
            // $order->region_id = $request->get('region_id') ?? null;

            // Обработка телефона
            $phone = $request->get('phone');
            if ($phone) {
                try {
                    $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone = preg_replace('/[^0-9+]/', '', $phone) ?: null;
                }
            }


            // Цена и UTM-метки (часто приходят из Albato как часть данных)
            $order->price = $request->get('price') ?? null;
            $order->utm_source = $request->get('utm_source') ?? 'vk';
            $order->type_id = $request->get('type_id') ?? 23;
            $order->utm_medium = $request->get('utm_medium') ?? 'cpc';
            $order->utm_campaign = $request->get('utm_campaign') ?? null;
            $order->utm_term = $request->get('utm_term') ?? null;
            $order->utm_content = $request->get('utm_content') ?? null;

            // Дополнительные поля Albato / VK Ads

            $order->referrer = $request->get('referrer') ?? 'https://vk.com/ads';
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = $request->get('source_id') ?? 174;;
            $order->comment = $request->get('comment') ?? null;;

            // Сохраняем заказ
            $order->save();

            // Запускаем событие
            OrderCreated::dispatch($order);

            return response()->json(['message' => 'Lead created', 'order_id' => $order->id, 'showroom_id' => $order->showroom_id], 201);

        } catch (Throwable $exception) {
            Log::error('VK Ads Dist', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'error create lead dist',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function vkDistributeKrim(Request $request)
    {
        try {

            //Log::info($request);
            $order = new Order;
            $order->client_name = $request->get('first_name', 'Новый клиент') . ' ' . $request->get('last_name', '');

            $site_id = $request->get('site_id');

            $last = DB::table('last_queue')->where('site_id', $site_id)->first();

            $percent = $last->percent ?? 0;
            //$showroomIndex = $this->getNext();


            $showrooms = array(15 => 17, 17 => 15);
            $sites = array(15 => 7124, 17 => 7594);


            if ($last->number === 15 && $percent < 50) {
                $percent += 50;
                $showroomIndex = 15;
            } else {
                $percent = 50;
                $showroomIndex = $this->getNextShowroomId($showrooms, $last->number);
            }

            DB::table('last_queue')
                ->where('site_id', $site_id) // Assuming 'id' is the primary key
                ->update(['number' => $showroomIndex, 'percent' => $percent, 'updated_at' => now()]);


            //$order->showroom_id = $request->get('showroom_id');;

            //$order->site_id = $request->get('site_id') ?? null;;

            $order->showroom_id = $showroomIndex;

            $order->site_id = $sites[$showroomIndex];


            $order->status_id = 1; // Статус "новый"
            $order->type_id = 1;
            // $order->region_id = $request->get('region_id') ?? null;

            // Обработка телефона
            $phone = $request->get('phone');
            if ($phone) {
                try {
                    $order->phone = PhoneNumber::make($phone, 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone = preg_replace('/[^0-9+]/', '', $phone) ?: null;
                }
            }


            // Цена и UTM-метки (часто приходят из Albato как часть данных)
            $order->price = $request->get('price') ?? null;
            $order->utm_source = $request->get('utm_source') ?? 'vk';
            $order->type_id = $request->get('type_id') ?? 23;
            $order->utm_medium = $request->get('utm_medium') ?? 'cpc';
            $order->utm_campaign = $request->get('utm_campaign') ?? null;
            $order->utm_term = $request->get('utm_term') ?? null;
            $order->utm_content = $request->get('utm_content') ?? null;

            // Дополнительные поля Albato / VK Ads

            $order->referrer = $request->get('referrer') ?? 'https://vk.com/ads';
            $order->entry_point = $request->get('entry_point') ?? null;
            $order->source_id = $request->get('source_id') ?? 175;;
            $order->comment = $request->get('comment') ?? null;;

            // Сохраняем заказ
            $order->save();

            // Запускаем событие
            OrderCreated::dispatch($order);

            return response()->json(['message' => 'Lead created', 'order_id' => $order->id, 'showroom_id' => $order->showroom_id], 201);

        } catch (Throwable $exception) {
            Log::error('VK Ads Dist C1', [
                'request' => $request->all(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'error create lead dist',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    private function getNext($currentIndex): int
    {
        return $this->showrooms[$currentIndex] ?? 2;
    }

    private function getNextShowroomId($showrooms, $currentIndex): int
    {
        return $showrooms[$currentIndex] ?? $currentIndex;
    }
}
