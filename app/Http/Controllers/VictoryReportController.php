<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Auth\PermissionController;
use App\Models\OrderStatus;
use App\Models\Permission;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class VictoryReportController extends Controller
{
    public function byOperator(Request $request)
    {
        //return $request;
        $query = "";
        $query_type = "";
        $query_showroom = "";
        $query_agency = "";
        $payment_method = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }

        if ($request->agency_id !== null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id = " . $request->agency_id . " AND o.site_id = " . $request->site_id;
        } else if ($request->agency_id !== null && $request->site_id === null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id = " . $request->agency_id;
        } else if ($request->agency_id === null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND o.site_id = " . $request->site_id;
        }

        //Log::debug($query . $payment_method . $query_showroom . $query_agency);

        $result = DB::select("SELECT CONCAT(u.first_name, ' ', u.last_name) as name, COUNT(*) AS total,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN o.status_id = 14 THEN 1 ELSE 0 END) AS AutoAnswerCount,
            SUM(CASE WHEN (o.status_id = 1000) THEN 1 ELSE 0 END) AS CopiedCount,
            SUM(CASE WHEN ((o.status_id IN (2,4,5,6,9,10) OR o.status_id IS NULL)) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.call_count = 0 || o.call_count is NULL ) THEN 1 ELSE 0 END) AS ZeroCount,
            SUM(CASE WHEN (o.call_count = 1) THEN 1 ELSE 0 END) AS OneCount,
            SUM(CASE WHEN (o.call_count = 2) THEN 1 ELSE 0 END) AS TwoCount,
            SUM(CASE WHEN (o.call_count = 3) THEN 1 ELSE 0 END) AS ThreeCount,
            SUM(CASE WHEN (o.call_count = 4) THEN 1 ELSE 0 END) AS FourCount,
            SUM(CASE WHEN (o.call_count = 5) THEN 1 ELSE 0 END) AS FiveCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id`" . $query . $payment_method . $query_showroom . $query_agency . "
            GROUP BY u.id
            ORDER BY CopiedCount desc
            ");
        return response()->json($result);
    }

    public function byOperatorPv(Request $request)
    {
        //return $request;
        $query = "";
        $query_type = "";
        $query_showroom = "";
        $query_agency = "";
        $payment_method = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }

        if ($request->agency_id !== null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id = " . $request->agency_id . " AND o.site_id = " . $request->site_id;
        } else if ($request->agency_id !== null && $request->site_id === null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id = " . $request->agency_id;
        } else if ($request->agency_id === null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND o.site_id = " . $request->site_id;
        }

        //Log::debug($query . $payment_method . $query_showroom . $query_agency);

        $result = DB::select("SELECT CONCAT(u.first_name, ' ', u.last_name) as name, sh.name as showroom,  COUNT(*) AS total,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN o.status_id = 14 THEN 1 ELSE 0 END) AS AutoAnswerCount,
            SUM(CASE WHEN (o.status_id = 1000) THEN 1 ELSE 0 END) AS CopiedCount,
            SUM(CASE WHEN ((o.status_id IN (2,4,5,6,9,10) OR o.status_id IS NULL)) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.call_count = 0 || o.call_count is NULL ) THEN 1 ELSE 0 END) AS ZeroCount,
            SUM(CASE WHEN (o.call_count = 1) THEN 1 ELSE 0 END) AS OneCount,
            SUM(CASE WHEN (o.call_count = 2) THEN 1 ELSE 0 END) AS TwoCount,
            SUM(CASE WHEN (o.call_count = 3) THEN 1 ELSE 0 END) AS ThreeCount,
            SUM(CASE WHEN (o.call_count = 4) THEN 1 ELSE 0 END) AS FourCount,
            SUM(CASE WHEN (o.call_count = 5) THEN 1 ELSE 0 END) AS FiveCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id`" . $query . $payment_method . $query_showroom . $query_agency . "
            INNER JOIN showrooms sh on u.showroom_id = sh.id and o.showroom_id in (18, 19, 20, 21,22,23,24,25,26,27)
            GROUP BY u.id
            ORDER BY sh.name
            ");
        return response()->json($result);
    }

    public function bySite(Request $request)
    {
        // return$request;
        $query = "";
        $payment_method = "";
        $query_agency = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->agency_id !== null) {
            $query_agency = " INNER JOIN sites y on o.site_id = y.id
            AND y.agency_id = " . $request->agency_id;
        }
        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.id, s.title, s.showroom_id,  COUNT(o.site_id) AS total
                FROM sites s
                LEFT JOIN orders o ON o.site_id = s.id AND o.`retries` IS NULL" . $query . $payment_method . $query_agency . "
           Where s.showroom_id = $request->showroom_id  GROUP BY s.id");
        return response()->json($result);
    }

    public function byAgency(Request $request)
    {
        //return $request;
        $query = "";
        $payment_method = "";
        $query_showroom = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }
        if ($request->site_id !== null) {
            $query_site = " AND s.id=" . $request->site_id;
        }
        if ($request->payment_method) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }


        $result = DB::select("SELECT COUNT(o.id) AS total, SUM(CASE WHEN s.agency_id = 1 THEN 1 ELSE 0 END) AS JustWe, SUM(CASE WHEN s.agency_id = 2 THEN 1 ELSE 0 END) AS StoUp, SUM(CASE WHEN s.agency_id = 3 THEN 1 ELSE 0 END) AS Victory, SUM(CASE WHEN s.agency_id = 4 THEN 1 ELSE 0 END) AS Classified, SUM(CASE WHEN s.agency_id = 5 THEN 1 ELSE 0 END) AS Seo,  SUM(CASE WHEN s.agency_id = 6 THEN 1 ELSE 0 END) AS Agency1,  SUM(CASE WHEN o.retries IS NOT NULL THEN 1 ELSE 0 END) AS Trash, SUM(CASE WHEN s.agency_id IS NULL THEN 1 ELSE 0 END) AS Others FROM sites s LEFT JOIN orders o ON o.site_id = s.id " . $query . $payment_method . $query_showroom . $query_site);
        return response()->json($result);
    }

    public function byStatus(Request $request)
    {

        $query = "";
        $payment_method = "";
        $query_showroom = "";
        $query_site = "";
        $query_agency = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        } else {
            $query_showroom = " AND o.showroom_id=9";
        }

        if ($request->agency_id !== null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites y on o.site_id = y.id
            AND y.agency_id = " . $request->agency_id . " AND o.site_id = " . $request->site_id;
        } else if ($request->agency_id !== null && $request->site_id === null) {
            $query_agency = " INNER JOIN sites y on o.site_id = y.id
            AND y.agency_id = " . $request->agency_id;
        } else if ($request->agency_id === null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites y on o.site_id = y.id
            AND o.site_id = " . $request->site_id;
        }

        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }
        $result = DB::select("SELECT
            s.id, s.name,datuses s
            INNER JOIN orders o ON  o.`status_id` = s.`id`
            " . $payment_method . $query_showroom . $query_site . $query_agency . $query . "
            where  o.`status_id` NOT IN (2,4,5,6,9,10)
            GROUP BY s.id");
        return response()->json($result);
    }

    public function byTrash(Request $request)
    {
        $query = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " WHERE  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->type_id !== null) {
            $query_type = " AND type_id = " . $request->type_id;
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }
        $result = DB::select("SELECT COUNT(*) AS total,
			SUM(CASE WHEN status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=15) THEN 1 ELSE 0 END) AS NotAppliedCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=16) THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=17) THEN 1 ELSE 0 END) AS CancelCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=18) THEN 1 ELSE 0 END) AS DepCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=19) THEN 1 ELSE 0 END) AS NoArriveCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=20) THEN 1 ELSE 0 END) AS ReviewCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=21) THEN 1 ELSE 0 END) AS NoCitizenshipCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=22) THEN 1 ELSE 0 END) AS AdsCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=23) THEN 1 ELSE 0 END) AS NoArriveVolgogradCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=24) THEN 1 ELSE 0 END) AS RepeatCount,
            SUM(CASE WHEN (status_id = 7  AND  trash_id=25) THEN 1 ELSE 0 END) AS NoDialCount
            from orders
            GROUP BY trash_id" . $query . $query_type . $query_showroom);
        return response()->json($result);
    }

    public function bySites(Request $request)
    {

        $query = "";
        $query_showroom = "";
        $query_agency = "";
        $payment_method = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, COUNT(*) AS total,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . "
           Where s.showroom_id= $request->showroom_id GROUP BY s.id");
        return response()->json($result);
    }

    public function byDates(Request $request)
    {

        $query = "";
        $query_agency = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        $result = DB::select("SELECT DATE(o.created_at) AS created, SUM(CASE WHEN o.type_id = 1 THEN 1 ELSE 0 END) AS CallCount,
                SUM(CASE WHEN o.type_id = 2 OR o.type_id <> 1  THEN 1 ELSE 0 END) AS CreditCount, count(o.id) AS total FROM orders o INNER JOIN sites s ON o.`site_id` = s.`id` " . $query_agency . $query . "
                where o.showroom_id = $request->showroom_id GROUP BY DATE(o.created_at) ORDER BY DATE(o.created_at)");
        return response()->json($result);
    }

    public function byContent(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_content, COUNT(*) AS order_counts,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . $query_site . "
           WHERE o.showroom_id= $request->showroom_id and o.utm_content <> '' GROUP BY s.id, o.utm_content");
        return response()->json($result);
    }

    public function byCampaign(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_campaign, COUNT(*) AS order_counts,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . $query_site . "
           WHERE o.showroom_id= $request->showroom_id and o.utm_campaign <> '' GROUP BY s.id, o.utm_campaign");
        return response()->json($result);
    }

    public function bySource(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_source, COUNT(*) AS order_counts,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . $query_site . "
           WHERE o.showroom_id= $request->showroom_id and o.utm_source <> '' GROUP BY s.id, o.utm_source");
        return response()->json($result);
    }

    public function byMedium(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_medium, COUNT(*) AS order_counts,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . $query_site . "
           WHERE o.showroom_id= $request->showroom_id and o.utm_medium <> '' GROUP BY s.id, o.utm_medium");
        return response()->json($result);
    }

    public function byTerm(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_term, COUNT(*) AS order_counts,
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
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency . $query_site . "
           WHERE o.showroom_id= $request->showroom_id and o.utm_term <> '' GROUP BY s.id, o.utm_term");
        return response()->json($result);
    }

    public function byUtm(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT o.id, o.created_at, o.utm_source, o.utm_campaign, o.utm_medium, o.utm_content, s.title, t.name, r.name as trash
            FROM orders o
            INNER JOIN sites s ON  o.`site_id` = s.`id`
            INNER JOIN order_statuses t ON  o.`status_id` = t.`id`
            LEFT JOIN order_trashes r ON  o.`trash_id` = r.`id`
           WHERE o.showroom_id= $request->showroom_id " . $query . $payment_method . $query_agency . $query_site . "  and ( r.id = o.`trash_id` OR o.`trash_id` IS NULL OR o.`trash_id` IS NULL)  and o.utm_source <> '' GROUP BY o.id");
        return response()->json($result);
    }

    public function extend(Request $request)
    {

        $query = "";
        $query_agency = "";
        $payment_method = "";
        $query_site = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . $request->agency_id;
        }

        if ($request->site_id !== null) {
            $query_site = " AND s.id = " . $request->site_id;
        }

        if ($request->paymenst_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        $result = DB::select("SELECT s.title, o.utm_campaign,o.utm_content, o.utm_source, COUNT(*) AS order_counts,
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
            FROM orders o
            INNER JOIN sites s ON o.site_id = s.id
           WHERE o.showroom_id= $request->showroom_id " . $query . $payment_method . $query_agency . $query_site . " and o.utm_content <> '' GROUP BY s.title, o.utm_campaign, o.utm_content, o.utm_source");
        return response()->json($result);
    }

    public function operator(Request $request)
    {
        //return $request;
        $query = "";
        $query_type = "";
        $query_showroom = "";
        $query_agency = "";
        $payment_method = "";
        $idList = [];
        if (is_array($request->agency_id)) {
            $idList = implode(',', $request->agency_id);
            $agency = " IN ($idList)";
        } else {
            $agency = " = $request->agency_id";
        }

        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }

        if ($request->agency_id !== null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id $agency AND o.site_id = " . $request->site_id;
        } else if ($request->agency_id !== null && $request->site_id === null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND s.agency_id  $agency";
        } else if ($request->agency_id === null && $request->site_id !== null) {
            $query_agency = " INNER JOIN sites s on o.site_id = s.id
            AND o.site_id = " . $request->site_id;
        }
        $result = DB::select("SELECT u.id, u.showroom_id, CONCAT(u.first_name, ' ', u.last_name) as name, COUNT(*) AS total,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN (o.status_id = 1000) THEN 1 ELSE 0 END) AS PassedCount,
            SUM(CASE WHEN (o.call_count = 0 || o.call_count is NULL ) THEN 1 ELSE 0 END) AS ZeroCount,
            SUM(CASE WHEN (o.call_count = 1) THEN 1 ELSE 0 END) AS OneCount,
            SUM(CASE WHEN (o.call_count = 2) THEN 1 ELSE 0 END) AS TwoCount,
            SUM(CASE WHEN (o.call_count = 3) THEN 1 ELSE 0 END) AS ThreeCount,
            SUM(CASE WHEN (o.call_count = 4) THEN 1 ELSE 0 END) AS FourCount,
            SUM(CASE WHEN (o.call_count = 5) THEN 1 ELSE 0 END) AS FiveCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id`" . $query . $payment_method . $query_showroom . $query_agency . "
            GROUP BY u.id");
        return response()->json($result);
    }


}
