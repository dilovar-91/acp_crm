<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Transit extends Model
{
    public function car()
    {
        return $this->belongsTo('App\Models\Car');
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
        return $query->whereRelation('car', 'mark_id', '=', $mark);
    }

    public function scopeCreatedBetween($query, $date1, $date2)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $date1)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $date2)->endOfDay();
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeShowroom(Builder $query, $showroom_id)
    {
        return $query->where('showroom_id', $showroom_id)->orWhere('from', $showroom_id);
    }
}
