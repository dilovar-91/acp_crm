<?php

namespace App\Http\Controllers;

use App\Events\CreditCreated;
use App\Events\SaleCreditProcessed;
use App\Models\CreditRequest;
use App\Models\Showroom;
use App\Models\User;
use App\Notifications\NotifyCancelOperator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CreditRequestController extends Controller
{

    public $today;
    public $startOfMonth;

    public function __construct()
    {
        $this->startOfMonth = new Carbon('first day of this month');
        $this->today = Carbon::today()->format('Y-m-d');
    }


    public function creditRequests(Request $request)
    {
        /*$trashed = $request->trashed === true ?? false;
        //$data = CreditRequest::with(['mark', 'region', 'region_live', 'manager', 'manager2', 'showroom'])->withTrashed($trashed)->whereBetween('date', [$filter_from, $filter_to])->where('showroom_id', '=', $showroom_id)->whereNull('is_sale')->orderby('created_at')->search($search)->get();
        */
        $cars = QueryBuilder::for(CreditRequest::class)
            ->allowedFilters([AllowedFilter::scope('withTrashed'), AllowedFilter::scope('search'), AllowedFilter::scope('withTrashed'), AllowedFilter::scope('between'), AllowedFilter::exact('showroom_id')])->with(['mark', 'region', 'region_live', 'manager', 'manager2', 'showroom', 'site', 'parent'])->whereNull('is_sale')->orderby('created_at')->get();
        return response()->json($cars, 201);

        $filter_from = $request->from ?? $this->startOfMonth->format('Y-m-d');
        $filter_to = $request->to ?? $this->today;
        $search = $request->search ?? "";
        $trashed = $request->trashed === true ?? false;
        //return response()->json($trashed);
        $data = CreditRequest::with(['mark', 'region', 'region_live', 'manager', 'manager2', 'showroom'])->withTrashed($trashed)->whereBetween('date', [$filter_from, $filter_to])->where('showroom_id', '=', $showroom_id)->whereNull('is_sale')->orderby('created_at')->search($search)->get();
        return response()->json($data, 201);
    }

    public function creditAllRequests(Request $request): JsonResponse
    {
        // return response()->json($request);
        $trashed = $request->trashed === 'true' ?? false;
        $filter_from = $request->from ?? $this->today;
        $filter_to = $request->to ?? $this->today;
        $search = $request->search ?? "";
        $data = CreditRequest::with(['mark', 'region', 'region_live', 'manager', 'manager2', 'showroom', 'operator'])->withTrashed($trashed)->whereBetween('date', [$filter_from, $filter_to])->orderby('showroom_id')->whereNull('is_sale')->orderby('created_at')->search($search)->get();
        return response()->json($data, 201);
    }

    public function sales(Request $request): JsonResponse
    {

        $sales = QueryBuilder::for(CreditRequest::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::exact('sale_vote_id'), AllowedFilter::scope('withTrashed'),AllowedFilter::scope('search'), AllowedFilter::scope('saleBetween')])->with(['mark', 'sale_manager', 'oformitel', 'bank'])->get();
        return response()->json($sales, 201);
    }

    public function saveCreditRequest(Request $request)
    {
        //return response()->json($request, 201);
        if (isset($request->item['id'])) {
            $credit_request = CreditRequest::withTrashed()->find($request->item['id']);
            if ($request->item['client_canceled'] === true && $request->item['showroom_id'] === 1) {
                $user = User::find(1)->first();
                $user->notify(new NotifyCancelOperator($request->item, -1001325677589));
                $user->notify(new NotifyCancelOperator($request->item, -1001606609572));
                //$user->notify(new NotifyCancelOperator($request->item, -628871825));
            } else if ($request->item['client_canceled'] === true && $request->item['showroom_id'] === 2) {
                $user = User::find(1)->first();
                //$user->notify(new NotifyCancelOperator($request->item, -1001463298841));
                //$user->notify(new NotifyCancelOperator($request->item, -1001765805481));
                //$user->notify(new NotifyCancelOperator($request->item, -795544349));
            }
        } else $credit_request = new CreditRequest;
        if (isset($request->item['date'])) {
            $credit_request->date = date('Y-m-d', strtotime($request->item['date']));
        }
        if ($request->item['transit'] === true && $request->item['transit_confirmed'] !== true) {
            $this->sendNotify("Кредитный отдел", $request->item['showroom_id'], $request->item['client_name']);
            $credit_request->transit = $request->item['transit'];
        } else if ($request->item['transit'] === 1 && $request->item['transit_confirmed'] === true) {
            $this->sendNotifyOperator("Кредитный отдел", $request->item['showroom_id'], $request->item['client_name']);
        } else {
            $credit_request->transit = $request->item['transit'];
        }
        $credit_request->date = $request->item['date'];
        $credit_request->client_name = $request->item['client_name'];
        $credit_request->showroom_id = $request->item['showroom_id'];
        $credit_request->region_id = $request->item['region_id'];
        $credit_request->live_region = $request->item['live_region'];
        $credit_request->mark_id = $request->item['mark_id'];
        $credit_request->model_id = $request->item['model_id'];
        $credit_request->phone_number = $request->item['phone_number'];
        $credit_request->price = $request->item['price'];
        $credit_request->initial_fee = $request->item['initial_fee'];
        $credit_request->manager_id = $request->item['manager_id'];
        $credit_request->manager2_id = $request->item['manager2_id'];
        $credit_request->comments = $request->item['comments'];
        $credit_request->sd_comment = $request->item['sd_comment'];
        $credit_request->absolute = $request->item['absolute'];
        $credit_request->expo = $request->item['expo'];
        $credit_request->sovkom = $request->item['sovkom'];
        $credit_request->oranzh = $request->item['oranzh'];
        $credit_request->tinkoff = $request->item['tinkoff'];
        $credit_request->kvant = $request->item['kvant'];
        $credit_request->zenit = $request->item['zenit'];
        $credit_request->loco_bank = $request->item['loco_bank'];
        $credit_request->cetelem = $request->item['cetelem'];
        $credit_request->vtb = $request->item['vtb'];
        $credit_request->uralsib = $request->item['uralsib'];
        $credit_request->otkritie = $request->item['otkritie'] ?? null;
        $credit_request->alfa_bank = $request->item['alfa_bank'] ?? null;
        $credit_request->bistro_bank = $request->item['bistro_bank'];

        $credit_request->absolute_manager = $request->item['absolute_manager'];
        $credit_request->loco_manager = $request->item['loco_manager'];
        $credit_request->expo_manager = $request->item['expo_manager'];
        $credit_request->sovkom_manager = $request->item['sovkom_manager'];
        $credit_request->oranzh_manager = $request->item['oranzh_manager'];
        $credit_request->tinkoff_manager = $request->item['tinkoff_manager'];
        $credit_request->kvant_manager = $request->item['kvant_manager'];
        $credit_request->zenit_manager = $request->item['zenit_manager'];
        $credit_request->cetelem_manager = $request->item['cetelem_manager'];
        $credit_request->vtb_manager = $request->item['vtb_manager'];
        $credit_request->uralsib_manager = $request->item['uralsib_manager'];
        $credit_request->otkritie_manager = $request->item['otkritie_manager'] ?? null;
        $credit_request->alfa_bank_manager = $request->item['alfa_bank_manager'];
        $credit_request->bistro_bank_manager = $request->item['bistro_bank_manager'];
        $credit_request->canceled = $request->item['canceled'];
        $credit_request->client_canceled = $request->item['client_canceled'];
        $credit_request->fssp_canceled = $request->item['fssp_canceled'];
        $credit_request->jump = $request->item['jump'];
        $credit_request->transit_confirmed = $request->item['transit_confirmed'];

        $credit_request->source_credit = $request->item['source_credit'] ?? null;
        $credit_request->operator_id = $request->item['operator_id'] ?? null;
        $credit_request->is_tradein = $request->item['is_tradein'] ?? null;
        $credit_request->site_id = $request->item['site_id'] ?? null;
        $credit_request->save();
        CreditCreated::dispatch($credit_request);
        return response()->json($credit_request, 201);
    }


    public function saleCreditRequest(Request $request)
    {
        //return $request->item->client_name;

        if (!isset($request->item['id'])) {
            $credit_request = new CreditRequest();
            $credit_request->client_name = $request->item['client_name'];
            $credit_request->date = $request->item['date'] ?? date('Y-m-d');
            $credit_request->showroom_id = $request->item['showroom_id'];
            $credit_request->phone_number = $request->item['phone_number'];
        } else {
            $credit_request = CreditRequest::withTrashed()->find($request->item['id']);
        }
        if ($request->item['sale_date'] !== null) {
            $credit_request->sale_date = date('Y-m-d', strtotime($request->item['sale_date']));
            $credit_request->is_sold = 1;
        } else $credit_request->sale_date = null;
        $credit_request->sale_bank_id = $request->item['sale_bank_id'] ?? null;
        $credit_request->car_name = $request->item['car_name'];
        if (isset($request->item['client_name'])) {
            $credit_request->client_name = $request->item['client_name'];
        }
        $credit_request->sale_vin = $request->item['sale_vin'] ?? null;
        $credit_request->sale_manager_id = $request->item['sale_manager_id'] ?? null;
        $credit_request->sale_oformitel_id = $request->item['sale_oformitel_id'] ?? null;
        if (isset($request->item['sale_call_date'])) {
            if ($request->item['sale_call_date'] !== null) {
                $credit_request->sale_call_date = date('Y-m-d', strtotime($request->item['sale_call_date']));
            } else $credit_request->sale_call_date = null;
        }else $credit_request->sale_call_date = null;
        if (isset($request->item['sale_recall_date'])) {
            //Log::emergency(date('Y-m-d H:i:00', strtotime($request->item['sale_recall_date'])));
            if ($request->item['sale_recall_date'] !== null) {
                $credit_request->sale_recall_date = date('Y-m-d H:i:00', strtotime($request->item['sale_recall_date']));
            } else $credit_request->sale_recall_date = null;
        }else $credit_request->sale_recall_date = null;
        $credit_request->sale_repeat = $request->item['sale_repeat'] ?? null;
        $credit_request->sale_vote_id = $request->item['sale_vote_id'] ?? null;
        $credit_request->sale_control = $request->item['sale_control'] ?? null;
        $credit_request->sale_comment = $request->item['sale_comment'] ?? null;
        $credit_request->save();
        $credit_request->delete();
        SaleCreditProcessed::dispatch($credit_request);
        CreditCreated::dispatch($credit_request);
        return response()->json($credit_request, 201);
    }

    public function deleteCreditRequests(Request $request)
    {
        //return $request;
        $credit_request = CreditRequest::find($request->item['id']);
        if ($request->item['is_sold'] === true || $request->item['is_sold'] === 1) {
            //$credit_request->sale_date = $request->item['sale_date'];
            if (isset($request->item['sale_date'])) {
                $credit_request->sale_date = date('Y-m-d', strtotime($request->item['sale_date']));
                $credit_request->is_sold = $request->item['is_sold'];
            }
            $credit_request->sale_bank_id = $request->item['sale_bank_id'];
            $credit_request->car_name = $request->item['car_name'];
            $credit_request->sale_vin = $request->item['sale_vin'];
            $credit_request->sale_manager_id = $request->item['sale_manager_id'];
            $credit_request->sale_repeat = $request->item['sale_repeat'];
            $credit_request->sale_oformitel_id = $request->item['sale_oformitel_id'];
            $credit_request->save();
            $credit_request->delete();

            CreditCreated::dispatch($credit_request);
        } else if (isset($credit_request)) {
            $credit_request->delete();
            CreditCreated::dispatch($credit_request);
        }
        return response()->json($credit_request, 200);
    }

    public function deleteSoldCreditRequests(Request $request)
    {
        //return $request;
        $credit_request = CreditRequest::withTrashed()->find($request->id);
        //return $credit_request;
        if (!empty($credit_request)) {
            $credit_request->sale_bank_id = null;
            $credit_request->car_name = null;
            $credit_request->sale_vin = null;
            $credit_request->sale_manager_id = null;
            $credit_request->sale_repeat = null;
            $credit_request->sale_date = null;
            $credit_request->is_sold = null;
            $credit_request->deleted_at = null;
            $credit_request->save();
            CreditCreated::dispatch($credit_request);
        }
        return response()->json($credit_request, 200);
    }

    public function syncCreditRequest(Request $request)
    {
        //return $request;
        $credit_request = CreditRequest::find($request->id);
        if (!empty($credit_request)) {
            $credit = $credit_request->replicate();
            $credit->parent_id  = $credit_request->id;
            $credit->client_name = $request->name;
            $credit->save();
            CreditCreated::dispatch($credit);
            return response()->json($credit, 200);
        } else {
            return response()->json(["message"=>"error"], 200);
        }
    }

    public function deleteTrashCreditRequests(Request $request)
    {

        $credit_request = CreditRequest::withTrashed()->find($request->id);
        $credit_request->forceDelete();
        return response()->json($credit_request, 200);
    }

    public function sendNotify($type, $showroom_id, $client_name)
    {
        if ($showroom_id === 1) {
            $roles = [3, 16];
        } else if ($showroom_id === 2) {
            $roles = [4, 17];
        }
        $users = User::whereIn('role_id', $roles)->get();
    }

    public function sendNotifyOperator($type, $showroom_id, $client_name)
    {
        if ($showroom_id === 1) {
            $roles = [4, 17];
        } else if ($showroom_id === 2) {
            $roles = [3, 16];
        }
        $users = User::whereIn('role_id', $roles)->get();
    }

    public function banks()
    {
        $items = Bank::orderBy('order')->orderBy('name')->get();
        return $items;
    }

    public function showrooms()
    {
        $showrooms = Showroom::get();
        return response()->json($showrooms, 201);
    }

    public function restore(Request $request)
    {
        $record = CreditRequest::withTrashed()->find($request->id);

        if ($record) {
            $record->restore();
        };
        return response()->json($record);
    }


}
