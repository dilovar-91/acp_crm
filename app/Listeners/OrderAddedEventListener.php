<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Blacklist;
use App\Models\Operator;
use App\Models\User;
use App\Models\Order;
use App\Services\BlacklistService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Helpers\GeneralHelper;
use Throwable;

class OrderAddedEventListener
{
    /**
     * Create the event listener.
     *
     */
    protected $blacklistService;

    public function __construct(BlacklistService $blacklistService)
    {
        $this->blacklistService = $blacklistService;
    }

    /**
     * Handle the event.
     *
     * @param OrderCreated $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        if ($event->order) {
            $phone = $event->order->phone;
            $order = Order::find($event->order->id);

            if ($order) {
                $this->blacklistService->checkPhoneInBlacklistOrSpam($phone, $order);

                // Получаем количество заказов с тем же телефоном и в том же шоуруме
                $resCount = Order::phone4($phone)
                    ->where('showroom_id', $event->order->showroom_id)
                    ->count();

                if ($resCount > 1) {
                    // Проверяем оператора и при необходимости присваиваем
                    if ($order->operator_id === null || $order->operator_id === 1000) {
                        $owner = Order::where('id', '<>', $order->id)
                            ->where('showroom_id', $order->showroom_id)
                            ->phone4($phone)
                            ->first();

                        if ($owner && $owner->operator_id !== null && $this->checkUserSchedule($owner->operator_id)) {
                            $order->operator_id = $owner->operator_id;
                        } else {
                            $order->operator_id = $this->distribute($order->showroom_id);
                        }
                    }

                    $order->retries = $resCount - 1;

                } else {
                    // Если заказ новый и оператор еще не назначен
                    if ($order->operator_id === null || $order->operator_id === 1000) {
                        $order->operator_id = $this->distribute($order->showroom_id);
                    }
                }

                $order->phone = $phone;
                $order->save();
            }
        }
    }


    public function distribute($showroom_id)
    {
        $from = null;
        $to = null;

        $ot = Carbon::now();
        $do = Carbon::now();
        if ($showroom_id === 9) {
            $ot->hour = 18;
            $ot->minute = 30;
        } else {
            $ot->hour = 19;
            $ot->minute = 00;
        }
        $ot->second = 00;
        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $from = Carbon::now();
            $to = Carbon::now()->addDays(1);
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
            $from = Carbon::now()->subDays(1);
            $to = Carbon::now();
        }

        if ($showroom_id === 9) {
            $from->hour = 18;
            $from->minute = 30;
            $to->hour = 18;
            $to->minute = 30;
        } else {
            $from->hour = 19;
            $from->minute = 00;
            $to->hour = 19;
            $to->minute = 00;
        }
        $from->second = 00;
        $to->second = 00;


        $res = User::with(['schedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])
            ->whereIn('role_id', [2, 6])
            ->where('showroom_id', $showroom_id)
            ->whereHas('schedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->withCount([
                'orders' => function ($query) use ($from, $to) {
                    //$query->whereBetween('created_at', [$from, $to])->whereNull('retries');
                    $query->leftJoin('sites', 'orders.site_id', '=', 'sites.id')
                        ->where(function ($subquery) {
                            $subquery->where('sites.type_id', '<>', 4)
                                ->orWhereNull('sites.type_id');
                        })
                        ->where(function ($subquery) {
                            $subquery->whereNull('orders.site_id')
                                ->orWhereNotNull('orders.site_id'); // Include orders with site_id = NULL
                        })
                        ->whereBetween('orders.created_at', [$from, $to])
                        ->whereNull('orders.retries');
                }
            ])
            ->orderBy('orders_count')
            ->first();
        return $res->id ?? null;
    }

    public function teleBot($id)
    {
        $result = Order::where('id', $id)->first();
        $chatIds = array('-277174745');
        //$chatIds = array('277174745');
        if ($result && $result->entry_point) {
            $parse = parse_url($result->entry_point);
            $hostname = $parse['host'] ?? '';
            foreach ($chatIds as $chatId) {
                try {
                    Telegram::setAsyncRequest(false)
                        ->sendMessage(['chat_id' => $chatId, 'text' => "Клиент по имени \"$result->last_name\" оставил заявку на сайте $hostname (a-c77.ru)"]);
                } catch (Throwable $e) {
                    continue;
                }

            }
        }
    }

    public function checkUserSchedule($user_id)
    {
        $ot = Carbon::now();
        $do = Carbon::now();


        $ot->hour = 19;
        $ot->minute = 00;
        $ot->second = 00;

        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
        }

        $user = User::where('id', $user_id)->whereHas('schedule', function ($query) use ($today) {
            $query->where($today, 1);
        })
            ->first();
        return !empty($user);

    }

}
