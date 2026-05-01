<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\CalltouchDealUpdateService;
use App\Services\MangoDealUpdateService;
use Illuminate\Support\Facades\Log;

class DealUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle(
        CalltouchDealUpdateService $calltouchService,
        MangoDealUpdateService $mangoService
    ): void {
        //Log::error('DealUpdate start');

        $orderItem = Order::with(['status', 'arrival_status', 'trash', 'mark', 'model', 'type', 'operator', 'site'])->find($this->order->id);

        if (!$orderItem) {
            Log::error('Order not found for DealUpdate');
            return;
        }

        if ($orderItem->site?->calltouch_site_id) {

            //Log::error('$calltouchService->update');
            $calltouchService->update($orderItem);
        }

        if ($orderItem->site?->mango_site_id) {
            //Log::error('$mangoService->update');
            $mangoService->update($orderItem);
        }
    }
}
