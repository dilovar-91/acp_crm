<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DistributeService
{
    public function distribute($showroom_id)
    {
        $ot = Carbon::now()->setTime(19, 0, 0);
        $do = Carbon::now()->setTime(23, 59, 59);

        $check = Carbon::now()->between($ot, $do);
        $now = Carbon::now();

        if ($check) {
            $from = $now;
            $to = $now->copy()->addDay();
            $today = Str::lower($to->englishDayOfWeek);
        } else {
            $from = $now->copy()->subDay();
            $to = $now;
            $today = Str::lower($now->englishDayOfWeek);
        }

        $from->setTime(19, 0, 0);
        $to->setTime(19, 0, 0);

        $res = User::with(['programmaticSchedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])
            ->where('showroom_id', $showroom_id)
            ->whereHas('programmaticSchedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->withCount([
                'orders' => function ($query) use ($from, $to) {
                    $query->whereBetween('orders.created_at', [$from, $to])
                        ->whereNull('orders.retries');
                }
            ])
            ->orderBy('orders_count')
            ->first();

        return $res->id ?? null;
    }
}
