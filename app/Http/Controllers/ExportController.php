<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    // Your controller method
    public function getOrderData(Request $request)
    {

        $fromDate = $request->fromDate;
        if (empty($fromDate)) return;


        $orders = Order::select(
            'orders.id',
            'orders.uuid',
            'orders.site_id',
            'orders.status_id',
            'orders.phone',
            DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m-%d %H:%i:%s") as createdAt'),
            DB::raw('DATE_FORMAT(orders.updated_at, "%Y-%m-%d %H:%i:%s") as updatedAt'),
            'sites.url as domain',
            'order_statuses.name as status_name'
        )
            ->where('orders.created_at', '>=', $fromDate)
            ->join('sites', 'orders.site_id', '=', 'sites.id')
            ->where('sites.agency_id', '=', 7)
            ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
            ->orderBy('orders.id')
            ->get();
        return response()->json($orders);
    }
}
