<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Arrival extends Model
{
    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    public function mark()
    {
        return $this->belongsTo('App\Models\Brand');

    }
    public function manager()
    {
        return $this->belongsTo('App\Models\User', 'manager_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    //scopes
    public function scopeBetween($query, $date1, $date2)
    {
        return $query->whereBetween('date', [$date1, $date2]);
    }
}
