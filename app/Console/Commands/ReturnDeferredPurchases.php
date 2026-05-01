<?php

namespace App\Console\Commands;

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Console\Command;
use App\Models\DeferredPurchase;
use Carbon\Carbon;

class ReturnDeferredPurchases extends Command
{
    protected $signature = 'purchases:return-deferred';
    protected $description = 'Handle all deferred purchases where the return date is today';

    public function handle()
    {
        $today = Carbon::today();

        $purchases = DeferredPurchase::whereDate('return_date', $today)->where('returned', '<>', 1)->get();

        if ($purchases->isEmpty()) {
            $this->info('No deferred purchases to return today.');
            return 0;
        }

        $order_ids = [];

        foreach ($purchases as $purchase) {
            $originalOrder = Order::find($purchase->order_id);

            if (!$originalOrder) {
                $this->warn("Order not found for ID {$purchase->order_id}");
                continue;
            }

            $purchase->returned = true;
            $purchase->save();

            // Replicate order base data
            $newOrder = $originalOrder->replicate();
            $newOrder->created_at = now();
            $newOrder->updated_at = now();
            $newOrder->status_id = 1; // or whatever default
            $newOrder->site_id = 6969; // or whatever default
            $newOrder->operator_id = null; // or whatever default
            $newOrder->save();

            OrderCreated::dispatch($newOrder);

            $order_ids[] = $newOrder->id;


        }

        $this->info("Replicated order IDS: " . implode(',', $order_ids));

        return 0;
    }
}
