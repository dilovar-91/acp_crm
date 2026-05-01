<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function usedcar()
    {
        return $this->belongsTo('App\Models\UsedCar');
    }
}
