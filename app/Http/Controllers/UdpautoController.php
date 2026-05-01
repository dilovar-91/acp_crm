<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\JustweModel;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class UdpautoController extends Controller
{
    public function lead(Request $request)
    {
        Log::warning('Tilda');
        Log::warning($request);
        try {
            $order = new Order;
            $order->client_name = $request->Name ?? 'Новый клиент';
            $order->showroom_id = 1;
            $order->site_id = 6263;
            $order->status_id = 1;
            $order->type_id = 2;
            $car_name = null;
            $price = null;
            if (isset($request->products)) {
                foreach ($request->products as $car) {
                    $car_name = $car->name . ' ';
                    $price = $car->price;
                }
            }
            try {
                $order->phone = PhoneNumber::make($request->Phone, 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = $request->Phone;
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
}
