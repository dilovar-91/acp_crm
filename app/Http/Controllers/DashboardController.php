<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Arrival;
use App\Models\CreditRequest;
use App\Models\Car;
use App\Models\UsedCar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $items= QueryBuilder::for(Arrival::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::scope('between')])
            ->orderBy('date', 'desc')->get();
        return response()->json($items, 200);
    }
    public function orders(Request $request)
    {

        $result = DB::table('orders AS o')
            ->join('order_statuses AS os', 'o.status_id', '=', 'os.id')
            ->join('showrooms AS s', 'o.showroom_id', '=', 's.id')
            ->whereBetween('o.created_at', ['start_date', 'end_date'])
            ->groupBy('o.status_id', 'o.showroom_id')
            ->select('s.name', 'os.name', 'o.status_id',  'o.showroom_id', DB::raw('COUNT(o.id) AS order_count'))->get();
        return $result;

        $result = DB::select("SELECT
            COUNT(CASE WHEN showroom_id = 4 THEN 1 END) AS avrora_total,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 1 THEN 1 ELSE 0 END) AS AvroraNew,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 2 THEN 1 ELSE 0 END) AS AvroraOnProcces,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 3 THEN 1 ELSE 0 END) AS AvroraNoAnswer,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 4  THEN 1 ELSE 0 END) AS AvroraApproved,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 5  THEN 1 ELSE 0 END) AS AvroraWillArrive,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 6 THEN 1 ELSE 0 END) AS AvroraArrived,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 7 THEN 1 ELSE 0 END) AS AvroraTrash,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 8 THEN 1 ELSE 0 END) AS AvroraRetry,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 9 THEN 1 ELSE 0 END) AS AvroraSell,
            SUM(CASE WHEN showroom_id = 4 AND status_id = 10 THEN 1 ELSE 0 END) AS AvroraBreak
            FROM orders where created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "' ");

        $arrivals = DB::select("SELECT
            SUM(CASE WHEN showroom_id = 4 AND status_id = 1 THEN 1 ELSE 0 END) AS AvroraArrivals
            FROM orders where will_arrive BETWEEN '" . $request->from . "' AND '" . $request->to . "' ");

        //$result->concat($arrivals);
        $res = $result + $arrivals;

        return response()->json($res);
    }

    public function creditRequests(Request $request): JsonResponse
    {
        $items= QueryBuilder::for(CreditRequest::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::scope('between')])
            ->orderBy('date', 'desc')->get();
        return response()->json($items, 200);
    }


    public function used_cars(Request $request): JsonResponse
    {
        $items= QueryBuilder::for(UsedCar::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])
            ->get();
        return response()->json($items, 200);
    }

    public function cars(Request $request): JsonResponse
    {
        $cars= QueryBuilder::for(Car::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])
            ->get();
        return response()->json($cars, 200);
    }

    public function car_sales(Request $request): JsonResponse
    {
        $cars= QueryBuilder::for(Car::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])
            ->where('is_sold','1')->get();
        return response()->json($cars, 200);
    }
}
