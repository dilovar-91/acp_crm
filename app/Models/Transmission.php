<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transmission extends Model
{

    protected $hidden = ['created_at', 'updated_at'];
    public function usedcar()
    {
        return $this->hasMany('App\Models\UsedCar');
    }
}
