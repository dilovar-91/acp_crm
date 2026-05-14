<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SummaryReportController extends Controller
{
    public function all(Request $request)
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

        $result = DB::select("SELECT a.name, a.id as agency_id,  sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
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
            SUM(CASE WHEN (o.status_id = 12) THEN 1 ELSE 0 END) AS ApprovalCount,
            SUM(CASE WHEN o.status_id = 13 THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN o.status_id = 15 THEN 1 ELSE 0 END) AS UnliquidCount,
            SUM(CASE WHEN o.status_id = 16 THEN 1 ELSE 0 END) AS RetryDiCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.payment_method  IS NULL OR o.payment_method = 0 OR o.payment_method = 7) THEN 1 ELSE 0 END) AS UndefindedCount,
            SUM(CASE WHEN (o.payment_method = 1) THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 2 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditCount,
            SUM(CASE WHEN (o.payment_method = 3 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 4) THEN 1 ELSE 0 END) AS LisingCount,
            SUM(CASE WHEN (o.payment_method = 5) THEN 1 ELSE 0 END) AS NoCallCount,
            SUM(CASE WHEN (o.payment_method = 6) THEN 1 ELSE 0 END) AS RepeatCount,
            SUM(CASE WHEN (o.region_id = 86 OR o.region_id = 87) THEN 1 ELSE 0 END) AS LDNRCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN agencies a on a.id = s.agency_id
            where sh.id not in (6, 11, 9) and o.operator_id NOT IN (358) $query $query_agency $query_showroom
            GROUP BY a.id, sh.id
            Order by sh.id asc, a.name asc");
        return response()->json($result);
    }

    public function arrivals(Request $request)
    {
        $query_showroom = "";
        $query_agency = "";
        $between = "";
        if ($request->from !== null && $request->to !== null) {
            $between = " BETWEEN date('" . $request->from . "') AND date('" . $request->to . "')";
        }
        if ($request->showroom_id !== null) {
            $query_showroom = " AND o.showroom_id=" . $request->showroom_id;
        }
        if ($request->agency_id !== null) {
            $query_agency = " AND a.id = " . $request->agency_id;
        }
        $result = DB::select("SELECT a.name, sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
            SUM(CASE WHEN o.status_id = 5 and o.will_arrive $between  THEN 1 ELSE 0 END) AS WillArriveCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN agencies a on a.id = s.agency_id
            where sh.id not in (6, 9)  and o.operator_id NOT IN (358) $query_agency $query_showroom
            GROUP BY a.id, sh.id order by sh.id asc, a.name asc");
        return response()->json($result);
    }

    public function byOperator(Request $request)
    {

        $query = "";
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




        $result = DB::select("SELECT u.id, sh.name as showroom, u.showroom_id, CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) AS name, COUNT(*) AS total,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 2 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditCount,
            SUM(CASE WHEN (o.payment_method = 3 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditSaleCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id` and o.operator_id NOT IN (358)" . $query . $payment_method . $query_showroom . $query_agency . "
            INNER JOIN showrooms sh ON  o.`showroom_id` = sh.`id`
            where o.showroom_id not in (9)
            GROUP BY u.id, sh.name");
        return response()->json($result);
    }

    public function old(Request $request)
    {
        //return $request;
        $query = "";
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

        $result = DB::select("SELECT s.name, COUNT(*) AS total,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 2 THEN 1 ELSE 0 END) AS OnProcessCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 4 THEN 1 ELSE 0 END) AS ApprovedCount,
            SUM(CASE WHEN o.status_id = 5 THEN 1 ELSE 0 END) AS WillArriveCount,
            SUM(CASE WHEN o.status_id = 6 THEN 1 ELSE 0 END) AS ArrivedCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN o.status_id = 9 THEN 1 ELSE 0 END) AS SellCount,
            SUM(CASE WHEN o.status_id = 10 THEN 1 ELSE 0 END) AS BreakCount,
            SUM(CASE WHEN o.status_id = 11 THEN 1 ELSE 0 END) AS NewRegionCount,
            SUM(CASE WHEN o.status_id = 13 THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN o.status_id IS NULL THEN 1 ELSE 0 END) AS OtherCount
        FROM users u
        INNER JOIN orders o ON o.operator_id = u.id and o.operator_id NOT IN (358)
        INNER JOIN showrooms s ON s.id = o.showroom_id $query GROUP by s.id");
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
            INNER JOIN orders o on o.showroom_id = sh.id and o.operator_id NOT IN (358)
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN order_drops d on o.drop_id = d.id
            where sh.id not in (6,  9) $query $query_agency $query_showroom
            GROUP BY s.id, sh.id Order by sh.id asc, s.title asc");
        return response()->json($result);
    }

    public function victory(Request $request)
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

        $result = DB::select("SELECT s.title, s.id as site_id,  sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN o.status_id = 1000 THEN 1 ELSE 0 END) AS PassedCount,
            SUM(CASE WHEN ((o.status_id IN (2,4,5,6,9,10) OR o.status_id IS NULL)) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.call_count = 0) THEN 1 ELSE 0 END) AS ZeroCount,
            SUM(CASE WHEN (o.call_count = 1) THEN 1 ELSE 0 END) AS OneCount,
            SUM(CASE WHEN (o.call_count = 2) THEN 1 ELSE 0 END) AS TwoCount,
            SUM(CASE WHEN (o.call_count = 3) THEN 1 ELSE 0 END) AS ThreeCount,
            SUM(CASE WHEN (o.call_count = 4) THEN 1 ELSE 0 END) AS FourCount,
            SUM(CASE WHEN (o.call_count = 5) THEN 1 ELSE 0 END) AS FiveCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id and o.operator_id NOT IN (358)
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN agencies a on a.id = s.agency_id
            where sh.id = 9 $query $query_agency $query_showroom
            GROUP BY s.id, sh.id Order by sh.id asc, s.title asc");
        return response()->json($result);
    }

    public function victory_status(Request $request)
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

        $result = DB::select("SELECT s.title, s.id as site_id,  sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
            SUM(CASE WHEN o.status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
            SUM(CASE WHEN o.status_id = 3 THEN 1 ELSE 0 END) AS NoAnswerCount,
            SUM(CASE WHEN o.status_id = 7 THEN 1 ELSE 0 END) AS TrashCount,
            SUM(CASE WHEN o.status_id = 8 THEN 1 ELSE 0 END) AS RetryCount,
            SUM(CASE WHEN o.status_id = 1000 THEN 1 ELSE 0 END) AS PassedCount,
            SUM(CASE WHEN ((o.status_id IN (2,4,5,6,9,10) OR o.status_id IS NULL)) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.call_count = 0) THEN 1 ELSE 0 END) AS ZeroCount,
            SUM(CASE WHEN (o.call_count = 1) THEN 1 ELSE 0 END) AS OneCount,
            SUM(CASE WHEN (o.call_count = 2) THEN 1 ELSE 0 END) AS TwoCount,
            SUM(CASE WHEN (o.call_count = 3) THEN 1 ELSE 0 END) AS ThreeCount,
            SUM(CASE WHEN (o.call_count = 4) THEN 1 ELSE 0 END) AS FourCount,
            SUM(CASE WHEN (o.call_count = 5) THEN 1 ELSE 0 END) AS FiveCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN agencies a on a.id = s.agency_id
            where sh.id = 9 $query $query_agency $query_showroom
            GROUP BY s.id, sh.id Order by sh.id asc, s.title asc");
        return response()->json($result);
    }


    public function byOperators(Request $request)
    {

        $query = "";
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




        $result = DB::select("SELECT u.id, sh.name as showroom, u.showroom_id, CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) AS name, COUNT(*) AS total,
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
            SUM(CASE WHEN (o.payment_method  IS NULL OR o.payment_method = 0) THEN 1 ELSE 0 END) AS UndefindedCount,
            SUM(CASE WHEN (o.payment_method = 1) THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 4) THEN 1 ELSE 0 END) AS LisingCount,
            SUM(CASE WHEN (o.payment_method = 5) THEN 1 ELSE 0 END) AS NoCallCount,
            SUM(CASE WHEN (o.payment_method = 6) THEN 1 ELSE 0 END) AS RepeatCount,
            SUM(CASE WHEN (o.region_id = 86 OR o.region_id = 87) THEN 1 ELSE 0 END) AS LDNRCount
            FROM users u
            INNER JOIN orders o ON  o.`operator_id` = u.`id` and o.operator_id NOT IN (358)" . $query . $payment_method . $query_showroom . $query_agency . "
            INNER JOIN showrooms sh ON  o.`showroom_id` = sh.`id`
            where o.showroom_id not in (9)
            GROUP BY u.id, sh.name");
        return response()->json($result);
    }


    public function agencySites(Request $request)
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

        $result = DB::select("SELECT a.name, a.id as agency_id, s.title,  sh.name as showroom, COUNT(*) AS total, sh.id as showroom_id,
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
            SUM(CASE WHEN (o.status_id = 12) THEN 1 ELSE 0 END) AS ApprovalCount,
            SUM(CASE WHEN o.status_id = 13 THEN 1 ELSE 0 END) AS DropCount,
            SUM(CASE WHEN o.status_id = 15 THEN 1 ELSE 0 END) AS UnliquidCount,
            SUM(CASE WHEN o.status_id = 16 THEN 1 ELSE 0 END) AS RetryDiCount,
            SUM(CASE WHEN (o.status_id  IS NULL) THEN 1 ELSE 0 END) AS OtherCount,
            SUM(CASE WHEN (o.payment_method  IS NULL OR o.payment_method = 0 OR o.payment_method = 7) THEN 1 ELSE 0 END) AS UndefindedCount,
            SUM(CASE WHEN (o.payment_method = 1) THEN 1 ELSE 0 END) AS CashCount,
            SUM(CASE WHEN (o.payment_method = 2) THEN 1 ELSE 0 END) AS CreditCount,
            SUM(CASE WHEN (o.payment_method = 3) THEN 1 ELSE 0 END) AS CreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 2 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditCount,
            SUM(CASE WHEN (o.payment_method = 3 and o.approved = 1) THEN 1 ELSE 0 END) AS ApprovedCreditSaleCount,
            SUM(CASE WHEN (o.payment_method = 4) THEN 1 ELSE 0 END) AS LisingCount,
            SUM(CASE WHEN (o.payment_method = 5) THEN 1 ELSE 0 END) AS NoCallCount,
            SUM(CASE WHEN (o.payment_method = 6) THEN 1 ELSE 0 END) AS RepeatCount,
            SUM(CASE WHEN (o.region_id = 86 OR o.region_id = 87) THEN 1 ELSE 0 END) AS LDNRCount
            FROM showrooms sh
            INNER JOIN orders o on o.showroom_id = sh.id
            INNER JOIN sites s on o.site_id = s.id
            INNER JOIN agencies a on a.id = s.agency_id
            where sh.id not in (6, 11, 9) $query $query_agency $query_showroom
            GROUP BY sh.id, s.id
            Order by sh.id asc, a.name asc");
        return response()->json($result);
    }
}
