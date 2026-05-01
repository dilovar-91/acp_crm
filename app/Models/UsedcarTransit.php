<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UsedcarTransit extends Model
{
    public function usedcar()
    {
        return $this->belongsTo('App\Models\UsedCar', "usedcar_id");
    }
    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }
    public function sender()
    {
        return $this->belongsTo('App\Models\User', "sender_id");
    }
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', "receiver_id");
    }

    public function from_showroom()
    {
        return $this->belongsTo('App\Models\Showroom', "from");
    }
    public function scopeMark(Builder $query, $mark)
    {
        return $query->whereRelation('usedcar', 'mark_id', '=', $mark);
    }
    public function scopeShowroom(Builder $query, $showroom_id)
    {
        return $query->where('showroom_id', $showroom_id)->orWhere('from', $showroom_id);
    }

    public function scopeCreatedBetween($query, $date1, $date2)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $date1)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $date2)->endOfDay();
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

}
