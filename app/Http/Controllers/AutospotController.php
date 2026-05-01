<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Helpers\GeneralHelper;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\JustweModel;
use App\Models\Order;
use App\Models\Region;
use App\Models\Site;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;
use Illuminate\Support\Facades\Log;


class AutospotController extends Controller
{
    public function create(Request $request)
    {
        try {
            $order = new Order();


            $site = Site::where('autospot_id', $request->ext_id)->first();

            $order->showroom_id = $site->showroom_id ?? 12;
            $order->site_id = $site->id ?? null;
            $order->status_id = 1;
            $order->source_id = 316;

            // Базовые поля
            $order->client_name = $request->client_name ?? 'Новый клиент';

            try {
                $order->phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = isset($request->phone)
                    ? preg_replace('/[^0-9+]/', '', $request->phone)
                    : null;
            }

            // Тип заявки / авто
            // если у вас type_id определяется по type, то передаем car_type
            $order->type_id = isset($request->car_type)
                ? GeneralHelper::getType($request->car_type)
                : null;


            if (!empty($request->person_region)) {
                $region = Region::where('name', 'LIKE', '%' . trim($request->person_region) . '%')->first();

                if ($region) {
                    $order->region_id = $region->id;
                } else {
                    $commentParts[] = 'Регион клиента: ' . $request->person_region;
                }
            }
            $commentMark = null;
            $commentModel = null;

            if (!empty($request->brand)) {
                $getMark = Brand::where('name', 'LIKE', '%' . trim($request->brand) . '%')->first();
                $order->mark_id =  $getMark->id ?? null;
                $commentMark = trim(($request->brand ?? ''));
            }
            if (!empty($request->model)) {
                $getModel = CarModel::search(trim($request->model))->first();
                $order->model_id =  $getModel->id ?? null;
                $commentModel = trim(($request->model ?? ''));
            }




            // $order->site_id = $request->site_id ?? null;
            $order->history = $request->history ?? null;
            $order->referrer = $request->referrer ?? null;
            $order->entry_point = $request->entry_point ?? null;


            // Комплектация
            if (is_array($request->equipment)) {
                $order->complectation = implode(', ', $request->equipment);
            } else {
                $order->complectation = $request->equipment ?? $request->complectation ?? null;
            }

            // Собираем всё, чего нет в модели, в комментарий


            if (!empty($request->comment)) {
                $commentParts[] = 'Комментарий из вебхука: ' . $request->comment;
            }
            if (!empty($request->alt_comment)) {
                $commentParts[] = 'Доп. инфо от клиента: ' . $request->alt_comment;
            }

            if (($request->car_type ?? null) === 'used') {
                $commentParts[] = 'Тип машины: Б.У.';
            } else {
                $commentParts[] ='Тип машины: Новая';
            }

            if (!empty($commentMark)) {
                $commentParts[] = 'Марка: ' . $commentMark;
            }
            if (!empty($commentModel)) {
                $commentParts[] = 'Марка: ' . $commentModel;
            }

            if (!empty($request->brand) && empty($order->mark_id)) {
                $commentParts[] = 'Марка: ' . $request->brand;
            }

            if (!empty($request->model) && empty($order->model_id)) {
                $commentParts[] = 'Модель: ' . $request->model;
            }

            if (!empty($request->person_amount)) {
                $commentParts[] = 'Бюджет клиента>: ' . $request->person_amount;
            }

            if (!empty($request->dealer_name)) {
                $commentParts[] = 'Салон дилера: ' . $request->dealer_name;
            }

            if (!empty($request->vin)) {
                $commentParts[] = 'VIN: ' . $request->vin;
            }





            if (!empty($request->engine)) {
                $commentParts[] = 'Двигатель: ' . $request->engine;
            }

            if (!empty($request->engine_value)) {
                $commentParts[] = 'Объем двигателя: ' . $request->engine_value;
            }

            if (!empty($request->transmission)) {
                $commentParts[] = 'Коробка передач: ' . $request->transmission;
            }

            if (!empty($request->wheels)) {
                $commentParts[] = 'Привод: ' . $request->wheels;
            }

            if (!empty($request->testdrive)) {
                $commentParts[] = 'Тест-драйв: ' . $request->testdrive;
            }

            if (!empty($request->color)) {
                $commentParts[] = 'Цвет авто: ' . $request->color;
            }

            if (!empty($request->buy_who)) {
                $commentParts[] = 'Покупка на: ' . $request->buy_who;
            }

            if ($request->has('fleet')) {
                $commentParts[] = 'Юр. лицо: ' . ((bool)$request->fleet ? 'Да' : 'Нет');
            }

            if (!empty($request->payment_method)) {
                $commentParts[] = 'Способ оплаты: ' . $request->payment_method;
            }

            if (!empty($request->person_region)) {
                $commentParts[] = 'Регион клиента: ' . $request->person_region;
            }

            if (!empty($request->alt_models)) {
                $altModels = is_array($request->alt_models)
                    ? implode(', ', $request->alt_models)
                    : $request->alt_models;

                $commentParts[] = 'Альтернативные модели: ' . $altModels;
            }

            if (!empty($request->alt_comment)) {
                $commentParts[] = 'Доп. информация по комплектации: ' . $request->alt_comment;
            }

            if (!empty($request->dealer_comment)) {
                $commentParts[] = 'Комментарий для менеджера дилера: ' . $request->dealer_comment;
            }

            if ($request->has('is_prepayment')) {
                $commentParts[] = 'Лид с предоплатой: ' . ((bool)$request->is_prepayment ? 'Да' : 'Нет');
            }

            if (!empty($request->time_of_purchase)) {
                $commentParts[] = 'Время покупки: ' . $request->time_of_purchase;
            }

            if (!empty($request->visit_dealer)) {
                $commentParts[] = 'Готовность к визиту в ДЦ: ' . $request->visit_dealer;
            }

            // Поля только для used
            if (($request->car_type ?? null) === 'used') {



                if (!empty($request->start_year)) {

                    $commentParts[] = 'Год производства от: ' . $request->start_year;
                }

                if (!empty($request->end_year)) {
                    $commentParts[] = 'Год производства до: ' . $request->end_year;
                }

                if (!empty($request->used_run)) {
                    $commentParts[] = 'Пробег до: ' . $request->used_run;
                }

                if (!empty($request->used_count_owners)) {
                    $commentParts[] = 'Количество владельцев: ' . $request->used_count_owners;
                }

                if (!empty($request->options_wanted)) {
                    $commentParts[] = 'Интересующие опции: ' . $request->options_wanted;
                }

                if (!empty($request->used_car_color)) {
                    $commentParts[] = 'Цвет кузова: ' . $request->used_car_color;
                }

                if ($request->has('used_credit_ready')) {
                    $commentParts[] = 'Готов к кредиту за хороший авто: ' . $request->used_credit_ready;
                }
            }

            $order->comment = implode("\n", $commentParts);

            $order->save();

            OrderCreated::dispatch($order);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error('Autospot webhook error', [
                'request' => $request->all(),
                'message' => $e->getMessage(),
            ]);

            report($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
