<?php

namespace App\Http\Controllers;

use App\Events\ArrivalCreated;
use App\Events\OrderCreated;
use App\Events\OrderProcessed;
use App\Exports\ArriveExport;
use App\Exports\InhouseExport;
use App\Exports\NoAnswerExport;
use App\Jobs\CallTouchDealUpdate;
use App\Jobs\DealUpdate;
use App\Models\ActivityLog;
use App\Models\Blacklist;
use App\Models\BlacklistOrder;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\DeferredPurchase;
use App\Models\JustweBrand;
use App\Models\JustweModel;
use App\Models\MissedCall;
use App\Models\Order;
use App\Models\OrderArrival;
use App\Models\OrderStatus;
use App\Models\OrderTrash;
use App\Models\OrderDrop;
use App\Models\OrderType;
use App\Models\SmsTemplate;
use App\Models\User;
use Carbon\Carbon as Carbon2;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class OrderController extends Controller
{
    public function orders(Request $request)
    {
        $user = Auth::user();
        $per_page = 10;
        $order = $request->order ?? 'created_at';

        $orders = QueryBuilder::for(Order::class)
            ->allowedIncludes(['blacklist'])
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::exact('site_id'), AllowedFilter::exact('copied'), AllowedFilter::exact('type_id'), AllowedFilter::exact('payment_method'), AllowedFilter::exact('status_id'), AllowedFilter::exact('arrival_id'), AllowedFilter::exact('trash_id'), AllowedFilter::exact('operator_id'), AllowedFilter::exact('region_id'), AllowedFilter::exact('mark_id'), AllowedFilter::exact('model_id'), AllowedFilter::scope('between'), AllowedFilter::scope('betweenUpdated'), AllowedFilter::scope('agency'), AllowedFilter::scope('betweenArrive'), AllowedFilter::scope('creditCount'), AllowedFilter::scope('approvedCredit'), AllowedFilter::scope('paymentUndefined'), AllowedFilter::scope('repeat'), AllowedFilter::scope('betweenArrived'), AllowedFilter::scope('betweenArrived'), AllowedFilter::scope('betweenCallback'), AllowedFilter::scope('LastCall'), AllowedFilter::scope('search'), AllowedFilter::scope('searchFio'), AllowedFilter::scope('tell'), AllowedFilter::exact('arrived'), AllowedFilter::exact('utm_campaign'), AllowedFilter::scope('notConfirmed'), AllowedFilter::scope('ArrivedType'), AllowedFilter::scope('blacklist'), AllowedFilter::scope('utm')])->with(['region', 'site', 'operator', 'showroom', 'type', 'status', 'mark', 'model', 'source', 'trash', 'arrival_status', 'deferPurchase']);;

        //$orders = $orders->paginate($per_page);
        //return response()->json($orders, 200);
        if ($user->role_id === 2 && (array_key_exists('betweenCallback', (array)$request->filter) || array_key_exists('betweenArrive', (array)$request->filter))) {
            $orders->where('operator_id', $user->id);
        } else if ($user->role_id === 2 && !array_key_exists('search', (array)$request->filter) && !array_key_exists('between', (array)$request->filter)) {
            $orders->where('operator_id', $user->id);
        }
        if (!array_key_exists('search', (array)$request->filter)) {
            $orders->orderBy($order, 'DESC');
        } else {
            $orders->orderBy('created_at', 'DESC');
        }
        $allowedRoleIds = [1, 3];

        if (!in_array($user->role_id, $allowedRoleIds)) {
            $orders->whereNotIn('status_id', [9, 10]);
        }
        $orders = $orders->paginate($per_page);
        return response()->json($orders, 200);
    }


    public function light_orders(Request $request)
    {
        $user = Auth::user();
        $per_page = 10;
        $order = $request->order ?? 'created_at';

        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('showroom_id'),
                AllowedFilter::exact('site_id'),
                AllowedFilter::exact('copied'),
                AllowedFilter::exact('type_id'),
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('trash_id'),
                AllowedFilter::exact('operator_id'),
                AllowedFilter::exact('mark_id'),
                AllowedFilter::exact('model_id'),
                AllowedFilter::scope('between'),
                AllowedFilter::scope('repeat'),
                AllowedFilter::scope('search'),
                AllowedFilter::scope('tell'),
                AllowedFilter::scope('ToShowroom'),
                AllowedFilter::scope('ArrivedType'),
                AllowedFilter::scope('deferred_purchase')
            ])
            ->with([
                'site:id,title,description',
                'operator:id,first_name,last_name',
                'status:id,name',
                'mark:id,name',
                'model:id,name',
            ])
            ->select([
                'id',
                'showroom_id',
                'site_id',
                'copied',
                'type_id',
                'status_id',
                'trash_id',
                'operator_id',
                'mark_id',
                'model_id',
                'line_number',
                'created_at',
                'updated_at',
                'complectation',
                'phone',
                'client_name',
                'comment',
                'call_count',
                'callback',
                'retries',
                'arrived_date',
                'drop_id',
                'source_id',
                'general_comment',
                'arrived',
                'will_arrive',
                'initial_fee',
                'region_id',
                'payment_method',
                'last_call',
                'price',
                'entry_point',
                'copied_to',
                'call_heard',
                'utm_source'
            ]);
        //$orders = $orders->paginate($per_page);
        //return response()->json($orders, 200);
        if ($user->role_id === 2 && !array_key_exists('search', (array)$request->filter) && !array_key_exists('between', (array)$request->filter)) {
            $orders->where('operator_id', $user->id);
        }
        if (!array_key_exists('search', (array)$request->filter)) {
            $orders->orderBy($order, 'DESC');
        } else {
            $orders->orderBy('created_at', 'DESC');
        }
        $orders = $orders->paginate($per_page);
        return response()->json($orders, 200);
    }


    public function all_orders(Request $request)
    {
        //return $request;
        $showroom_id = $request->id;
        $orders = Order::select('id', 'status_id', 'type_id', 'callback', 'client_name', 'comment', 'phone', 'operator_id', 'showroom_id', 'will_arrive', 'created_at')->with(['operator'])->today()->showroom($showroom_id)->orderBy('created_at', 'DESC')->get();
        return response()->json($orders, 200);
    }

    public function stat_orders(Request $request)
    {
        $showroom_id = $request->id;
        $user_id = 135; // Auth::user()->id ?? -1;
        $result = DB::select("SELECT
			SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS NewCount,
			SUM(CASE WHEN status_id = 1 and operator_id = $user_id THEN 1 ELSE 0 END) AS MyNewCount,
			SUM(CASE WHEN status_id = 12 and date(will_arrive) = CURDATE()  THEN 1 ELSE 0 END) AS FiredCount,
			SUM(CASE WHEN status_id = 12 and operator_id = $user_id and date(will_arrive) = CURDATE() THEN 1 ELSE 0 END) AS MyFiredCount
            from orders
            where showroom_id = $showroom_id and ( created_at = CURDATE() or will_arrive = CURDATE() or callback = CURDATE() or status_id in (1,2) )");
        return response()->json($result);
    }


    public function all_arrivals(Request $request)
    {
        $showroom_id = $request->id;
        $orders = Cache::remember('showroom_orders_' . $showroom_id, now()->addMinutes(4), function () use ($showroom_id) {
            return Order::select('id', 'status_id', 'type_id', 'callback', 'client_name', 'comment', 'phone', 'operator_id', 'showroom_id', 'will_arrive', 'created_at')
                ->with(['operator'])
                ->showroom($showroom_id)
                ->whereDate('will_arrive', Carbon::now()->format('Y-m-d'))
                ->orderBy('created_at', 'DESC')
                ->get();
        });

        return response()->json($orders, 200);
    }

    public function missed_calls(Request $request)
    {
        //return $request;
        $showroom_id = $request->id;
        $today = Carbon::today()->format('Y-m-d');
        $calls = DB::select("SELECT m.id as missed_id, o.id, u.last_name, u.first_name, o.client_name, o.operator_id, o.phone, o.showroom_id, o.comment, o.general_comment, m.created_at FROM missed_calls m LEFT JOIN orders o on o.id = m.order_id LEFT JOIN users u on u.id = o.operator_id where o.showroom_id = $showroom_id and DATE(m.created_at)=CURDATE() and m.visited is null order by m.created_at desc");
        return response()->json($calls, 200);
    }

    public function detail($showroom, $id)
    {
        $user = Auth::user();
        $item = Order::with(['region', 'site', 'operator', 'showroom', 'type', 'missed_call', 'deferPurchase'])->where('showroom_id', $showroom)->where('id', $id)->firstOrFail();

        if ($user->id != 4 && $user->id != 1) {
            try {
                activity()->performedOn($item)->causedBy($user)->tap(function (Activity $activity) {
                    $activity->subject_type = 2;
                    $activity->causer_type = null;
                })->log(2);
            } catch (Throwable $e) {
                Log::warning($e);
            }
        }

        if ($item->missed_call?->id) {
            MissedCall::destroy($item->missed_call->id);
        }
        return response()->json($item, 200);
    }

    public function histories(Request $request)
    {
        $id = $request->id;
        $items = ActivityLog::with(['user'])->where('subject_type', 2)->where('subject_id', $id)->orderBy('created_at', 'DESC')->get();
        return response()->json($items, 200);
    }

    public function types(Request $request): JsonResponse
    {
        $key = 'types';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return OrderType::get();
        });
        return response()->json($items, 200);
    }

    public function statuses(Request $request): JsonResponse
    {
        $key = 'order_statuses'; // A unique cache key
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return OrderStatus::orderBy('order')->where('active', 1)->get();
        });
        return response()->json($items, 200);
    }

    public function trashes(Request $request): JsonResponse
    {

        $key = 'trashes'; // A unique cache key
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            // This closure will be executed if the cache key does not exist.
            return OrderTrash::orderBy('id')->get();
        });
        return response()->json($items, 200);
    }

    public function drops(Request $request): JsonResponse
    {

        $key = 'drops';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return OrderDrop::get();
        });
        return response()->json($items, 200);
    }

    public function arrival_statuses(Request $request): JsonResponse
    {

        $key = 'arrivals';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return OrderArrival::get();
        });
        return response()->json($items, 200);
    }

    public function repeats(Request $request)
    {
        //return $request;
        $id = $request->item['id'];
        $showroom_id = $request->item['showroom_id'];
        $phone = $request->item['phone'];
        $orders = Order::select('id', 'status_id', 'callback', 'client_name', 'created_at', 'retries', 'phone', 'showroom_id', 'comment')
            ->where('id', '<>', $id)
            ->with(['status'])
            ->search($phone)
            ->where('showroom_id', $showroom_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json($orders, 200);
    }


    //similar phones in all showroom
    public function all_repeats(Request $request)
    {
        //return $request;
        $id = $request->item['id'];
        $phone = $request->item['phone'];
        $orders = Order::select('id', 'status_id', 'callback', 'client_name', 'created_at', 'retries', 'phone', 'showroom_id', 'comment')
            ->where('id', '<>', $id)
            ->with(['status'])
            ->search($phone)
            ->orderBy('created_at', 'DESC')
            ->get();
        return response()->json($orders);
    }


    public function saveOrder(Request $request)
    {
        if (isset($request->item['id'])) {
            $order = Order::find($request->item['id']);
        } else $order = new Order;
        $order->client_name = $request->item['client_name'] ?? 'Новый клиент';
        $order->showroom_id = $request->item['showroom_id'];
        $order->site_id = $request->item['site_id'] ?? null;
        $order->status_id = $request->item['status_id'] ?? 1;
        $order->type_id = $request->item['type_id'] ?? 5;
        $order->region_id = $request->item['region_id'] ?? null;
        $order->car_status_id = $request->item['car_status_id'] ?? null;
        $order->mark_id = $request->item['mark_id'] ?? null;
        $order->model_id = $request->item['model_id'] ?? null;
        try {
            $order->phone = PhoneNumber::make($request->item['phone'], 'RU')->formatE164();
        } catch (Throwable $e) {
            $order->phone = $request->item['phone'];
        }
        $order->price = $request->item['price'] ?? null;
        if ($request->item['payment_method'] === 0) {
            $request->item['payment_method'] = 7;
        } else $order->payment_method = $request->item['payment_method'] ?? null;
        $order->initial_fee = $request->item['initial_fee'] ?? null;
        $order->operator_id = $request->item['operator_id'] ?? null;
        $order->callback = $request->item['callback'] ?? null;
        $order->last_call = $request->item['last_call'] ?? null;
        $order->will_arrive = $request->item['will_arrive'] ?? null;
        $order->comment = $request->item['comment'] ?? null;
        $order->general_comment = $request->item['general_comment'] ?? null;
        $order->ads_source = $request->item['ads_source'] ?? null;
        $order->country = $request->item['country'] ?? null;
        $order->complectation = $request->item['complectation'] ?? null;
        $order->car_year = $request->item['car_year'] ?? null;
        $order->credit_period = $request->item['credit_period'] ?? null;
        //$order->source_id = $request->item['credit_period'] ?? null;


        $order->save();
        OrderCreated::dispatch($order);
        DealUpdate::dispatch($order)->delay(now()->addMinutes(6));
        return response()->json($order, 201);
    }

    public function update(Request $request)
    {

        //$phone = PhoneNumber::make($request->item['phone'], 'RU');
        //return $phone->formatForCountry('RU');


        $order = Order::find($request->item['id']);
        if ($order) {
            $order->client_name = $request->item['client_name'];
            try {
                $order->phone = PhoneNumber::make($request->item['phone'], 'RU')->formatE164();
            } catch (Throwable $e) {
                $order->phone = $request->item['phone'];
            }

            if ($request->item['work_phone'] !== null) {
                try {
                    $order->work_phone = PhoneNumber::make($request->item['work_phone'], 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->work_phone = $request->item['work_phone'];
                }
            } else {
                $order->work_phone = null;
            }

            if ($request->item['phone_2'] !== null) {
                try {
                    $order->phone_2 = PhoneNumber::make($request->item['phone_2'], 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone_2 = $request->item['phone_2'];
                }
            } else {
                $order->phone_2 = null;
            }

            if ($request->item['phone_3'] !== null) {
                try {
                    $order->phone_3 = PhoneNumber::make($request->item['phone_3'], 'RU')->formatE164();
                } catch (Throwable $e) {
                    $order->phone_3 = $request->item['phone_3'];
                }
            } else {
                $order->phone_3 = null;
            }
            $order->showroom_id = $request->item['showroom_id'];
            if ($request->item['birthday'] !== null) {
                $order->birthday = Carbon::parse($request->item['birthday'])->format('Y-m-d');
            } else {
                $order->birthday = null;
            }
            if ($request->item['arrived_date'] !== null) {
                $order->arrived_date = Carbon::parse($request->item['arrived_date'])->format('Y-m-d');
            } else {
                $order->arrived_date = null;
            }
            $order->site_id = $request->item['site_id'] ?? null;
            $order->status_id = $request->item['status_id'];
            $order->type_id = $request->item['type_id'];
            $order->region_id = $request->item['region_id'];
            $order->car_status_id = $request->item['car_status_id'] ?? null;
            $order->mark_id = $request->item['mark_id'];
            $order->model_id = $request->item['model_id'];
            $order->price = $request->item['price'];
            if ($request->item['payment_method'] === 0) {
                $request->item['payment_method'] = 7;
            } else $order->payment_method = $request->item['payment_method'] ?? null;
            $order->initial_fee = $request->item['initial_fee'];
            $order->operator_id = $request->item['operator_id'];
            $order->callback = $request->item['callback'];
            //$order->last_call = $request->item['last_call'];

            if ($request->item['last_call'] !== null) {
                try {
                    $order->last_call = $request->item['last_call'];
                } catch (Throwable $e) {
                    Log::debug($e);
                }
            } else {
                $order->last_call = null;
            }


            $order->will_arrive = $request->item['will_arrive'];
            $order->comment = $request->item['comment'];
            $order->general_comment = $request->item['general_comment'];
            $order->ads_source = $request->item['ads_source'];
            $order->complectation = $request->item['complectation'] ?? null;
            $order->car_year = $request->item['car_year'] ?? null;
            $order->arrived = $request->item['arrived'] ?? null;
            $order->car_number = $request->item['car_number'] ?? null;
            $order->work_place = $request->item['work_place'] ?? null;
            $order->work_position = $request->item['work_position'] ?? null;
            $order->official_income = $request->item['official_income'] ?? null;
            $order->work_experience = $request->item['work_experience'] ?? null;
            $order->live_region = $request->item['live_region'] ?? null;
            $order->tradein_mark_id = $request->item['tradein_mark_id'] ?? null;
            $order->tradein_model_id = $request->item['tradein_model_id'] ?? null;
            $order->tradein_year = $request->item['tradein_year'] ?? null;
            $order->tradein_price = $request->item['tradein_price'] ?? null;

            $order->commercial_offer = $request->item['commercial_offer'] ?? null;
            $order->approved = $request->item['approved'] ?? null;
            $order->canceled = $request->item['canceled'] ?? null;

            $order->credit_period = $request->item['credit_period'] ?? null;
            $order->bank_1 = $request->item['bank_1'] ?? null;
            $order->bank_2 = $request->item['bank_2'] ?? null;
            $order->bank_3 = $request->item['bank_3'] ?? null;
            if ($request->item['percent_1']) {
                $order->percent_1 = floatval(str_replace(',', '.', $request->item['percent_1']));
            }
            if ($request->item['percent_2']) {
                $order->percent_2 = floatval(str_replace(',', '.', $request->item['percent_2']));
            }
            if ($request->item['percent_3']) {
                $order->percent_3 = floatval(str_replace(',', '.', $request->item['percent_3']));
            }
            $order->link_1 = $request->item['link_1'] ?? null;
            $order->link_2 = $request->item['link_2'] ?? null;

            $order->trash_id = $request->item['trash_id'] ?? null;
            $order->arrival_id = $request->item['arrival_id'] ?? null;
            if ($order->status_id !== 7) {
                $order->trash_id = null;
            }
            if ($order->status_id !== 6) {
                $order->arrival_id = null;
            }

            $order->call_count = $request->item['call_count'] ?? null;
            $order->call_heard = $request->item['call_heard'] ?? null;
            $order->save();
            OrderProcessed::dispatch($order);
            DealUpdate::dispatch($order)->delay(now()->addMinutes(6));
        }
        return response()->json($order, 201);
    }

    public function distribute(Request $request)
    {
        $order = Order::find($request->item['order_id']);
        $operator_id = $request->item['operator_id'] ?? null;
        $site_id = $request->item['site_id'] ?? null;
        $type_id = $request->item['type_id'] ?? 15;
        if (!empty($order)) {
            $newOrder = $order->replicate();
            $newOrder->showroom_id = $request->item['showroom_id']; // the new project_id
            $newOrder->operator_id = $operator_id;
            $newOrder->status_id = 1;
            $newOrder->retries = null;
            $newOrder->type_id = $type_id;
            $newOrder->last_call = null;
            $newOrder->callback = null;
            if ($site_id) {
                $newOrder->site_id = $site_id;
            }
            $newOrder->copied_to = null;
            $newOrder->save();
            if ($order->copied_to) {
                $order->copied_to = array_merge($order->copied_to, array($request->item['showroom_id']));
            } else {
                $order->copied_to = array($request->item['showroom_id']);
            }
            $order->copied = 1;
            $order->status_id = 1000;
            $order->save();
            OrderCreated::dispatch($newOrder);
            return response()->json($newOrder, 201);
        }
        return response()->json($order, 201);
    }


    public function copy(Request $request)
    {

        $order = Order::find($request->item['order_id']);
        if (!empty($order)) {
            $newOrder = $order->replicate();
            $newOrder->copied_from = $newOrder->showroom_id;
            $newOrder->showroom_id = $request->item['showroom_id']; // the new project_id
            $newOrder->operator_id = null;
            $newOrder->status_id = 1;
            $newOrder->retries = null;
            if ($order->site_id === 6333) {
                $newOrder->site_id = 6532;
            } else {
                $newOrder->site_id = 6226;
            }
            $newOrder->type_id = 18;
            $newOrder->last_call = null;
            $newOrder->callback = null;
            $newOrder->trash_id = null;
            $newOrder->arrived = null;
            $newOrder->will_arrive = null;
            $newOrder->arrived_date = null;
            $newOrder->date_of_sale = null;
            $newOrder->call_heard = null;
            $newOrder->line_number = null;
            $newOrder->call_count = null;
            $newOrder->commercial_offer = null;
            //$newOrder->link_1 = null;
            //$newOrder->link_2 = null;
            //$newOrder->percent_1 = null;
            //$newOrder->percent_2 = null;
            //$newOrder->percent_3 = null;
            //$newOrder->bank_1 = null;
            //$newOrder->bank_2 = null;
            //$newOrder->bank_3 = null;
            //$newOrder->approved = null;
            //$newOrder->canceled = null;
            //$newOrder->tradein_mark_id = null;
            //$newOrder->tradein_model_id = null;
            //$newOrder->tradein_year = null;
            //$newOrder->tradein_price = null;
            $newOrder->save();

            if ($order->copied_to) {
                $order->copied_to = array_merge($order->copied_to, array($request->item['showroom_id']));
            } else {
                $order->copied_to = array($request->item['showroom_id']);
            }
            $order->save();
            OrderCreated::dispatch($newOrder);
            return response()->json($newOrder, 201);
        }
        return response()->json($order, 201);
    }


    public function deferPurchase(Request $request)
    {

        $order = Order::find($request->item['order_id']);
        if (!empty($order)) {
            $newOrder = $order->replicate();
            $newOrder->copied_from = $request->item['showroom_id'];
            $newOrder->showroom_id = 28;
            $newOrder->operator_id = $request->item['operator_id'] ?? null;
            $newOrder->status_id = 1;
            $newOrder->retries = null;
            $newOrder->last_call = null;
            $newOrder->callback = null;
            $newOrder->trash_id = null;
            $newOrder->arrived = null;
            $newOrder->will_arrive = null;
            $newOrder->arrived_date = null;
            $newOrder->date_of_sale = null;
            $newOrder->call_heard = null;
            $newOrder->line_number = null;
            $newOrder->call_count = null;
            $newOrder->commercial_offer = null;
            $newOrder->save();
            $order->save();

            $deferredPurchase = DeferredPurchase::updateOrCreate(
                ['order_id' => $request->item['order_id']], // условия поиска
                [
                    'return_date' => $request->item['purchase_date'],
                    'returned' => $request->item['returned'] ?? false,
                ]
            );


            //OrderCreated::dispatch($newOrder);
            return response()->json($newOrder, 201);
        }
        return response()->json($order, 201);
    }

    public function deleteDeferred(Request $request)
    {
        $order = DeferredPurchase::where('order_id', $request->id)->first();

        if (!$order) {
            return response()->json(['message' => 'Заявка не найдена.'], 404);
        }
        $order->delete();

        return response()->json(['message' => 'Заявка удалена успешно.']);
    }


    public function passOrders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payload.operator_id' => 'required|integer',
            'payload.showroom_id' => 'required|integer',
            'payload.order_ids' => 'required|array|min:1',
            'payload.order_ids.*' => 'integer',
        ]);

        $orderIds = $request->input('payload.order_ids');
        $newOperatorId = $request->input('payload.operator_id');
        $showroomId = $request->input('payload.showroom_id');

        // Perform the update
        Order::whereIn('id', $orderIds)
            ->update(['operator_id' => $newOperatorId]);
        $newOrder = new Order();
        $newOrder->showroom_id = $showroomId;

        return response()->json(['message' => 'Orders updated successfully']);
    }

    public function update_mini(Request $request)
    {

        //$phone = PhoneNumber::make($request->item['phone'], 'RU');
        //return $phone->formatForCountry('RU');

        if (isset($request->item['id'])) {
            $order = Order::find($request->item['id']);
        } else $order = new Order;

        $order->client_name = $request->item['client_name'] ?? 'Новый клиент';
        try {
            $order->phone = PhoneNumber::make($request->item['phone'], 'RU')->formatE164();
        } catch (Throwable $e) {
            $order->phone = $request->item['phone'];
        }
        $order->showroom_id = $request->item['showroom_id'];

        if ($request->item['arrived_date'] !== null) {
            $order->arrived_date = Carbon::parse($request->item['arrived_date'])->format('Y-m-d');
        } else {
            $order->arrived_date = null;
        }
        $order->site_id = $request->item['site_id'] ?? null;
        $order->status_id = $request->item['status_id'] ?? 1;
        $order->trash_id = $request->item['trash_id'] ?? null;
        $order->type_id = $request->item['type_id'] ?? 1;
        $order->car_status_id = $request->item['car_status_id'] ?? null;
        $order->mark_id = $request->item['mark_id'] ?? null;
        $order->model_id = $request->item['model_id'] ?? null;
        $order->price = $request->item['price'] ?? null;
        if ($request->item['payment_method'] === 0) {
            $request->item['payment_method'] = 7;
        } else $order->payment_method = $request->item['payment_method'] ?? null;
        $order->region_id = $request->item['region_id'] ?? null;
        $order->initial_fee = $request->item['initial_fee'] ?? null;
        $order->operator_id = $request->item['operator_id'] ?? null;
        $order->callback = $request->item['callback'] ?? null;
        $order->last_call = $request->item['last_call'] ?? null;
        $order->will_arrive = $request->item['will_arrive'] ?? null;
        $order->call_heard = $request->item['call_heard'] ?? null;
        $order->comment = $request->item['comment'] ?? null;
        $order->arrived = $request->item['arrived'] ?? null;
        $order->general_comment = $request->item['general_comment'] ?? null;
        $order->source_id = $request->item['source_id'] ?? 3;
        if (isset($request->item['call_count'])) {
            $order->call_count = $request->item['call_count'] ?? null;
        }
        if ($order->status_id !== 7) {
            $order->trash_id = null;
        }

        $order->arrival_id = $request->item['arrival_id'] ?? null;
        if ($order->status_id !== 6) {
            $order->arrival_id = null;
        }
        $order->save();


        if (isset($request->item['id'])) {
            OrderProcessed::dispatch($order);
            DealUpdate::dispatch($order)->delay(now()->addMinutes(6));
        } else {
            OrderCreated::dispatch($order);
        };
        return response()->json($order, 201);
    }

    public function visited_mini(Request $request)
    {

        $user = Auth::user();
        $item = Order::with(['region', 'site', 'operator', 'showroom', 'type', 'missed_call'])->where('id', $request->id)->first();
        try {
            activity()->performedOn($item)->causedBy($user)->tap(function (Activity $activity) {
                $activity->subject_type = 2;
                $activity->causer_type = null;
            })->log(2);
        } catch (Throwable $e) {
            Log::warning($e);
        }
    }

    public function delete(Request $request)
    {
        $order = Order::find($request->id);
        if (isset($order)) {
            OrderProcessed::dispatch($order);
            $order->deleteOrder();
        }
        return response()->json($order, 200);
    }


    public function deleteTrashOrders(Request $request)
    {
        $order = Order::withTrashed()->find($request->id);
        $order->forceDelete();
        return response()->json($order, 200);
    }


    public function create(Request $request): JsonResponse
    {
        $order = new Order;
        $order->client_name = $request->client_name;
        $order->showroom_id = $request->showroom_id;
        $order->operator_id = $request->operator_id;
        $order->status_id = 1;
        $order->type_id = 1;
        $order->source_id = 5;

        $order->phone = $request->phone;
        $order->save();
        OrderCreated::dispatch($order);
        return response()->json($order, 201);
    }

    public function shedule()
    {
        $now = Carbon::now();
        $temp = Carbon::now();
        $temp->hour = 19;
        $temp->minute = 00;
        $temp->second = 00;

        if (Carbon::parse($now) > $temp) {
            $from = Carbon::now();
            $to = Carbon::now()->addDays(1);
        } else {
            $from = Carbon::now()->subDays(1);
            $to = Carbon::now();
        }

        $from->hour = 19;
        $from->minute = 00;
        $from->second = 00;
        $to->hour = 19;
        $to->minute = 00;
        $to->second = 00;
        $res = User::with('schedule')->where("active", 1)->whereIn('role_id', [2, 6])->withCount([
            'orders',
            'orders as count' => function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])->whereNull('retries');
            }
        ])->orderBy('count')->get();
        return $res;
    }


    public function check_phone(Request $request)
    {
        try {
            return PhoneNumber::make('+7 (512) 724 15 39', 'RU')->formatE164();
        } catch (Throwable $e) {
            return '+7 (512) 724 15 39';
        }
    }


    public function blacklist(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|integer'
        ]);

        $orderId = $request->input('id');
        $status = $request->input('status');

        $order = Order::where('id', $orderId)->first();


        BlacklistOrder::where('order_id', $orderId)
            ->update(['status_id' => $status]);

        Blacklist::updateOrCreate(
            ['phone' => preg_replace('/\D/', '', $order->phone)],  // Уникальное поле
            ['showroom_id' => $order->showroom_id]  // Обновить салон, если запись существует
        );

        return response()->json(['message' => 'Blacklist updated successfully']);
    }

    public function blacklistRequest(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);
        $order_id = $request->input('order_id');
        BlacklistOrder::updateOrCreate(
            ['order_id' => $order_id]
        );
        return response()->json(['message' => 'Blacklist Request created successfully']);
    }


    public function export(Request $request)
    {
        $from = $request->from ?? null;
        $to = $request->to ?? null;
        $showroom_id = $request->showroom_id ?? null;
        if (empty($from) || empty($to) || empty($showroom_id)) return;
        return (new NoAnswerExport($from, $to, $showroom_id))->download('Заявки со статусом НО.xlsx');
    }


    public function exportArrive(Request $request)
    {

        $from = $request->from ?? null;
        $to = $request->to ?? null;
        $showroom_id = $request->showroom_id ?? null;
        if (empty($from) || empty($to) || empty($showroom_id)) return;
        return (new ArriveExport($from, $to, $showroom_id))->download('Заявки со статусом приедет.xlsx');
    }


    public function deffered_orders(Request $request)
    {
        $user = Auth::user();
        $perPage = 10;
        $orderBy = $request->order ?? 'created_at';
        $filters = (array)$request->filter;

        $orders = QueryBuilder::for(Order::class)
            ->allowedIncludes(['blacklist'])
            ->allowedFilters([
                AllowedFilter::exact('showroom_id'),
                AllowedFilter::exact('site_id'),
                AllowedFilter::exact('copied'),
                AllowedFilter::exact('type_id'),
                AllowedFilter::exact('payment_method'),
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('arrival_id'),
                AllowedFilter::exact('trash_id'),
                AllowedFilter::exact('operator_id'),
                AllowedFilter::exact('region_id'),
                AllowedFilter::exact('mark_id'),
                AllowedFilter::exact('model_id'),
                AllowedFilter::exact('arrived'),
                AllowedFilter::exact('utm_campaign'),
                AllowedFilter::scope('between'),
                AllowedFilter::scope('betweenUpdated'),
                AllowedFilter::scope('agency'),
                AllowedFilter::scope('betweenArrive'),
                AllowedFilter::scope('creditCount'),
                AllowedFilter::scope('approvedCredit'),
                AllowedFilter::scope('paymentUndefined'),
                AllowedFilter::scope('repeat'),
                AllowedFilter::scope('betweenArrived'),
                AllowedFilter::scope('betweenCallback'),
                AllowedFilter::scope('LastCall'),
                AllowedFilter::scope('search'),
                AllowedFilter::scope('searchFio'),
                AllowedFilter::scope('tell'),
                AllowedFilter::scope('notConfirmed'),
                AllowedFilter::scope('blacklist'),
                AllowedFilter::scope('deferredPurchase'), // ← добавлен новый scope
                AllowedFilter::scope('deferredReturnDate'), // ← добавлен новый scope
                AllowedFilter::scope('returned'), // ← добавлен новый scope
            ])
            ->with([
                'region',
                'site',
                'operator',
                'showroom',
                'type',
                'status',
                'mark',
                'model',
                'source',
                'trash',
                'arrival_status',
                'deferPurchase',

            ]);

        // Фильтрация по оператору для роли 2
        if ($user->role_id === 2) {
            $shouldRestrictByOperator = isset($filters['betweenCallback']) || isset($filters['betweenArrive'])
                || (!isset($filters['search']) && !isset($filters['between']));

            if ($shouldRestrictByOperator) {
                $orders->where('operator_id', $user->id);
            }
        }

        // Установка порядка сортировки
        $orders->orderBy(
            isset($filters['search']) ? 'created_at' : $orderBy,
            'DESC'
        );

        // Исключить статусы 9 и 10 для ролей не из списка
        if (!in_array($user->role_id, [1, 3])) {
            $orders->whereNotIn('status_id', [9, 10]);
        }

        return response()->json($orders->paginate($perPage), 200);
    }


    public function inhouseExport(Request $request)
    {


        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $type_ids = (array)($request->type_id ?? []);
        $status_id = $request->status_id ?? null;
        $site_id = $request->site_id ?? null;
        $agency_id = $request->agency_id ?? null;


        $query = Order::with([
            'type', 'status', 'arrival_status', 'trash', 'region', 'mark',
            'model', 'site', 'source',
        ])
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->whereHas('site', function ($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->when(!empty($request->showroom_id), fn($q) => $q->where('showroom_id', $request->showroom_id))
            ->when(!empty($type_ids), fn($q) => $q->whereIn('type_id', $type_ids))
            ->when(!empty($status_id), fn($q) => $q->where('status_id', $status_id))
            ->when(!empty($site_id), fn($q) => $q->where('site_id', $site_id));

        $orders = $query->get();

        return Excel::download(new InhouseExport($orders), 'Orders.xlsx');
    }


    public function returnPurchased(Request $request)
    {
        $originalOrder = Order::where('id', $request->order_id)->first();

        if (!$originalOrder) {
            Log::info("Order not found for ID {$request->order_id}");
            return response()->json(['message' => 'Order returned not found'], 404);
        }

        $purchase = DeferredPurchase::where('order_id', $request->order_id)->first();

        $purchase->returned = true;
        $purchase->save();

        $newOrder = $originalOrder->replicate();
        $newOrder->created_at = now();
        $newOrder->updated_at = now();
        $newOrder->status_id = 1;
        $newOrder->site_id = 6969;
        $newOrder->operator_id = null;
        $newOrder->save();

        OrderCreated::dispatch($newOrder);

        return response()->json(['message' => 'Order returned successfully']);
    }
}
