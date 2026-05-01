<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class OrderDistributeService
{
    public function distribute($showroom_id)
    {
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
