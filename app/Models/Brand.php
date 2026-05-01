<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function cars()
    {
        return $this->hasMany('App\Models\Car');
    }

    public function models()
    {
        return $this->hasMany('App\Models\CarModel');
    }
    public function carprice()
    {
        return $this->hasMany('App\Models\CarPrice');
    }
    public function usedcar()
    {
        return $this->hasMany('App\Models\UsedCar');
    }

    public function creditrequest()
    {
        return $this->hasMany('App\Models\CreditRequest');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'mark_id');
    }
}
