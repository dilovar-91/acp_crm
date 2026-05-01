<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function creditrequest()
    {
        return $this->hasMany('App\Models\CreditRequest');
    }
    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }
    public function arrivals()
    {
        return $this->hasMany('App\Models\Arrival');
    }
    public function clients()
    {
        return $this->hasMany('App\Models\Client');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}
