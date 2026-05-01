<?php

namespace App\Jobs;

use App\Events\OrderCreated;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDistribute implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $showroom_id;
    protected $phone;
    /**
     * Отложенное распределение заявок.
     *
     * @return void
     */
    public function __construct($showroom_id, $phone)
    {
        $this->showroom_id = $showroom_id;
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order3 = Order::where('operator_id', 1000)->where('showroom_id', $this->showroom_id)->where('phone', '+' . $this->phone)->orWhere('phone', $this->phone)->latest()->first();
        if (!empty($order3)) {
            Log::warning('Dist end-' . $this->phone.  now()->format('Y-m-d H:i:s'));
            $order3->operator_id = null;
            $order3->client_name = "Пропущеный звонок";
            $order3->save();
            OrderCreated::dispatch($order3);
        }

    }


}
