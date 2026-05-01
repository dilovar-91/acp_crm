<?php

namespace App\Jobs;

use App\Models\CallCount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $operator_id;
    public $call_direction;
    /**
     * Отложенный отчет по кол-во звонков.
     *
     * @return void
     */
    public function __construct($order, $operator_id, $call_direction)
    {
        $this->order = $order;
        $this->operator_id = $operator_id;
        $this->call_direction = $call_direction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $item  = new CallCount();
        $item->order_id = $this->order->id;
        $item->showroom_id = $this->order->showroom_id;
        $item->operator_id = $this->operator_id;
        $item->call_direction = $this->call_direction;
        $item->save();
    }
}
