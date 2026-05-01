<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Text;

class OrderCountController extends Controller
{
    public function index(Request $request)
    {
        //return $request;
        $showroom_id = $request->id ??  1;
        $result = DB::select("SELECT s.name, o.utm_medium, COUNT(*) AS order_counts,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 2 THEN 1 ELSE 0 END) AS OnProccessCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 4  THEN 1 ELSE 0 END) AS ApprovedCount,
            SUM(CASE WHEN o.status_id = 5  THEN 1 ELSE 0 END) AS WillArriveCount,
            SUM(CASE WHEN (o.status_id = 6) THEN 1 ELSE 0 END) AS ArrivedCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN (o.status_id = 9) THEN 1 ELSE 0 END) AS SellCount,
            SUM(CASE WHEN (o.status_id = 10) THEN 1 ELSE 0 END) AS BreakCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM order_statuses s
            LEFT JOIN orders o ON  o.`status_id` = s.`id`
            LEFT JOIN operators p ON  o.`operator_id` = p.`id`
           WHERE o.showroom_id= $showroom_id GROUP BY s.id, p.id");
        return response()->json($result);
    }
}
