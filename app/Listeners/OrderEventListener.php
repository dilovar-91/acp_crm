<?php

namespace App\Listeners;

use App\Models\Order;
use App\Events\OrderProcessed;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;


class OrderEventListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param OrderProcessed $event
     * @return void
     */
    public function handle(OrderProcessed $event)
    {
        /*
                if ($event->order) {
                    $phone = $event->order->phone;

                    $res = Order::search($phone)->where('showroom_id', $event->order->showroom_id)->get();

                    $order = Order::where('id', $event->order->id)->first();

                    if (count($res) > 1) {
                        if (count($res) > 1) {
                            $order->retries = count($res) - 1;
                        }
                    }

                    if($event->order->work_phone !=null) {
                        $work_res = Order::search($event->order->work_phone)->where('showroom_id', $event->order->showroom_id)->get();
                        if (count($work_res) > 1) {
                                $order->retries += count($work_res) - 1;
                        }
                    }

            $order->save();
        }*/

    }

}
