<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complectation extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function car()
    {
        return $this->hasMany('App\Models\Car');
    }
}
