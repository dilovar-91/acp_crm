<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function usedcar()
    {
        return $this->hasMany('App\Models\UsedCar');
    }

    public function creditrequest()
    {
        return $this->hasMany('App\Models\CreditRequest');
    }
}
