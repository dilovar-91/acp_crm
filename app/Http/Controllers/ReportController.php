<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Auth\PermissionController;
use App\Models\OrderStatus;
use App\Models\Permission;
use App\Models\Site;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class ReportController extends Controller
{
    public function rest(Request $request)
    {
        $site = Site::where("phone", "74954775169")->orWhere("second_phone", "74954775169")->first();
        return $site;


    }
    public function byOperator(Request $request)
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
            $agency =" IN ($idList)";
        } else {
            $agency =" = $request->agency_id";
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

        //Log::alert($query_agency);





        $result = DB::select("SELECT u.id, u.showroom_id, CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) AS name, COUNT(*) AS total,
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 15) THEN 1 ELSE 0 END) AS UnliquidCount,
            SUM(CASE WHEN (o.status_id = 16) THEN 1 ELSE 0 END) AS RetryDiCount,
            SUM(CASE WHEN (o.payment_method  = 7 OR o.payment_method = 0) THEN 1 ELSE 0 END) AS UndefindedCount,
            SUM(CASE WHEN (o.payment_method = 1) THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 4) THEN 1 ELSE 0 END) AS LisingCount,
            SUM(CASE WHEN (o.payment_method = 5) THEN 1 ELSE 0 END) AS NoCallCount,
            SUM(CASE WHEN (o.payment_method = 6) THEN 1 ELSE 0 END) AS RepeatCount,
            SUM(CASE WHEN (o.payment_method = 10) THEN 1 ELSE 0 END) AS RepeatDiCount,
            SUM(CASE WHEN (o.region_id = 86 OR o.region_id = 87) THEN 1 ELSE 0 END) AS LDNRCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id`" . $query . $payment_method . $query_showroom . $query_agency . "
            GROUP BY u.id");
        return response()->json($result);
    }

    public function bySite(Request $request)
    {

        $query = "";
        $payment_method = "";
        $query_agency = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->agency_id !== null) {
            $agencyIds = implode(',', array_map('intval', $request->agency_id));

            $query_agency = " LEFT JOIN sites y on o.site_id = y.id
            AND y.agency_id in ($agencyIds) ";
        }
        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        Log::info("SELECT s.id, s.title, s.showroom_id,  COUNT(o.site_id) AS total
                FROM sites s
                LEFT JOIN orders o ON o.site_id = s.id AND o.`retries` IS NULL" . $query . $payment_method . $query_agency . "
           Where s.showroom_id = $request->showroom_id  GROUP BY s.id");

        $result = DB::select("SELECT s.id, s.title, s.showroom_id,  COUNT(o.site_id) AS total
                FROM sites s
                LEFT JOIN orders o ON o.site_id = s.id AND o.`retries` IS NULL " . $query . $payment_method . $query_agency . "
           Where s.showroom_id = $request->showroom_id  GROUP BY s.id");

        return response()->json($result);
    }





    public function bySiteAgency(Request $request)
    {
        $query = "";
        $payment_method = "";
        $query_agency = "";

        if ($request->from !== null && $request->to !== null) {
            $query = " AND o.created_at BETWEEN '{$request->from}' AND '{$request->to}'";
        }

        if ($request->agency_id !== null) {
            $query_agency = " AND s.agency_id = " . (int)$request->agency_id;
        }

        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . (int)$request->payment_method;
        }

        $result = DB::select("
                SELECT
                    s.id,
                    s.title,
                    s.showroom_id,
                    s.agency_id,
                    COUNT(o.id) AS total,

                    SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
                    SUM(CASE WHEN o.status_id = 2 THEN 1 ELSE 0 END) AS OnProccessCount,
                    SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
                    SUM(CASE WHEN o.status_id = 4 THEN 1 ELSE 0 END) AS ApprovedCount,
                    SUM(CASE WHEN o.status_id = 5 THEN 1 ELSE 0 END) AS WillArriveCount,
                    SUM(CASE WHEN o.status_id = 6 THEN 1 ELSE 0 END) AS ArrivedCount,
                    SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
                    SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
                    SUM(CASE WHEN o.status_id = 16 THEN 1 ELSE 0 END) AS RetryDiCount,
                    SUM(CASE WHEN o.status_id = 9 THEN 1 ELSE 0 END) AS SellCount,
                    SUM(CASE WHEN o.status_id = 10 THEN 1 ELSE 0 END) AS BreakCount,
                    SUM(CASE WHEN o.status_id = 11 THEN 1 ELSE 0 END) AS NewRegionCount,
                    SUM(CASE WHEN o.status_id = 13 THEN 1 ELSE 0 END) AS DropCount

                FROM sites s
                INNER JOIN orders o
                    ON o.site_id = s.id
                    AND o.retries IS NULL

                WHERE 1=1
                    $query
                    $payment_method
                    $query_agency

                GROUP BY s.id
            ");

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

        $result = DB::select("
                SELECT
                    COUNT(o.id) AS total,
                    SUM(CASE WHEN s.agency_id = 1 THEN 1 ELSE 0 END) AS JustWe,
                    SUM(CASE WHEN o.type_id = 12 THEN 1 ELSE 0 END) AS Whatsapp,
                    SUM(CASE WHEN s.agency_id = 3 THEN 1 ELSE 0 END) AS Victory,
                    SUM(CASE WHEN s.agency_id = 4 THEN 1 ELSE 0 END) AS Classified,
                    SUM(CASE WHEN s.agency_id = 5 THEN 1 ELSE 0 END) AS Seo,
                    SUM(CASE WHEN s.agency_id = 6 THEN 1 ELSE 0 END) AS Agency1,
                    SUM(CASE WHEN o.retries IS NOT NULL THEN 1 ELSE 0 END) AS Trash,
                    SUM(CASE WHEN s.agency_id = 9 THEN 1 ELSE 0 END) AS VA,
                    SUM(CASE WHEN s.agency_id IS NULL THEN 1 ELSE 0 END) AS Others
                FROM sites s
                INNER JOIN orders o
                    ON o.site_id = s.id
                $query
                $payment_method
                $query_showroom
                $query_site
            ");

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
            s.id, s.name, COUNT(*) AS total
            FROM order_statuses s
            INNER JOIN orders o ON  o.`status_id` = s.`id`
            " . $payment_method . $query_showroom . $query_site . $query_agency . $query . "
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

        if (!empty($request->agency_id)) {
            if (is_array($request->agency_id)) {
                $idList = implode(',', $request->agency_id);
                $query_agency = " AND s.agency_id IN ($idList)";
            } else {
                $query_agency = " AND s.agency_id = " . (int)$request->agency_id;
            }
        }

        if ($request->payment_method !== null) {
            $payment_method = " AND o.payment_method = " . $request->payment_method;
        }

        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id = " . $request->showroom_id;
        }

        $result = DB::select("SELECT s.title, s.id as site_id, COUNT(*) AS total,
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id = 15) THEN 1 ELSE 0 END) AS UnliquidCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id` " . $query . $payment_method . $query_agency . $query_showroom . "
           Where s.showroom_id= $request->showroom_id GROUP BY s.id");
        return response()->json($result);
    }

    public function payment(Request $request)
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
            SUM(CASE WHEN o.payment_method = 1 THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN o.payment_method = 2 THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN o.payment_method = 3 THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN o.payment_method = 4 THEN 1 ELSE 0 END) AS LeasingCount,
            SUM(CASE WHEN o.payment_method = 5 THEN 1 ELSE 0 END) AS NotDialingCount,
            SUM(CASE WHEN o.payment_method = 6 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN (o.payment_method = 7 OR o.payment_method IS NULL) THEN 1 ELSE 0 END) AS UndefinedCount,
            SUM(CASE WHEN o.payment_method = 8 THEN 1 ELSE 0 END) AS LdnrCount,
            count(o.id) AS total
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id` " . $query . $payment_method . $query_agency . "
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
                SUM(CASE WHEN o.type_id = 2 OR o.type_id <> 1  THEN 1 ELSE 0 END) AS CreditCount, count(o.id) AS total FROM orders o INNER JOIN sites s ON o.`site_id` = s.`id` ".$query_agency . $query. "
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency .$query_site . "
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

        $result = DB::select("SELECT o.site_id, s.title, o.utm_campaign, o.showroom_id, COUNT(*) AS order_counts,
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency .$query_site . "
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency .$query_site . "
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency .$query_site . "
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM sites s
            INNER JOIN orders o ON  o.`site_id` = s.`id`" . $query . $payment_method . $query_agency .$query_site . "
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
           WHERE o.showroom_id= $request->showroom_id " . $query . $payment_method . $query_agency .$query_site . "  and ( r.id = o.`trash_id` OR o.`trash_id` IS NULL OR o.`trash_id` IS NULL)  and o.utm_source <> '' GROUP BY o.id");
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
            SUM(CASE WHEN (o.status_id = 11) THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN (o.status_id = 13) THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount
            FROM orders o
            INNER JOIN sites s ON o.site_id = s.id
           WHERE o.showroom_id= $request->showroom_id " . $query . $payment_method . $query_agency .$query_site . " and o.utm_content <> '' GROUP BY s.title, o.utm_campaign, o.utm_content, o.utm_source");
        return response()->json($result);
    }


    public function approval(Request $request)
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
            $agency =" IN ($idList)";
        } else {
            $agency =" = $request->agency_id";
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

        //Log::alert($query_agency);





        $result = DB::select("SELECT u.id, u.showroom_id, CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) AS name, COUNT(*) AS total,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 2 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditCount,
            SUM(CASE WHEN (o.payment_method = 3 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditSaleCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id`" . $query . $payment_method . $query_showroom . $query_agency . "
            GROUP BY u.id");
        return response()->json($result);
    }

    public function drops(Request $request)
    {

        $query = "";
        $query_showroom = "";
        $query_agency = "";
        if ($request->from !== null && $request->to !== null) {
            $query = " AND  o.created_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }
        if ($request->agency_id !== null) {
            $query_agency = " AND a.id = " . $request->agency_id;
        }

        $result = DB::select("SELECT s.title, sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 1  THEN 1 ELSE 0 END) AS ReviewCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 2  THEN 1 ELSE 0 END) AS ChangedPaymentCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 3  THEN 1 ELSE 0 END) AS NotLeaveJobCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 4  THEN 1 ELSE 0 END) AS FoundCheapCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 5  THEN 1 ELSE 0 END) AS DontBelieveCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 6  THEN 1 ELSE 0 END) AS GoDealerCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 7  THEN 1 ELSE 0 END) AS AlreadyBoughtCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 8  THEN 1 ELSE 0 END) AS BoughtHistoryCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 9  THEN 1 ELSE 0 END) AS ChangedMindCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 10  THEN 1 ELSE 0 END) AS FamilySituationCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 11  THEN 1 ELSE 0 END) AS NoMoneyCount,
            SUM(CASE WHEN o.status_id = 13 and o.drop_id = 12  THEN 1 ELSE 0 END) AS NoDialingCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN order_drops d on o.drop_id = d.id
            where sh.id not in (6,  9) $query $query_agency $query_showroom
            GROUP BY s.id, sh.id Order by sh.id asc, s.title asc");
        return response()->json($result);
    }


}
