<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'models';
    protected $fillable = ['name', 'pic'];

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');

    }
    public function car()
    {
        return $this->hasMany('App\Models\Car');

    }

    public function model()
    {
        return $this->hasMany('App\Models\CarPrice');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'model_id');
    }

    public function scopeSearch($query, $searchTerm) {
        return $query
            ->where('name', 'like', "%" . $searchTerm . "%");
    }
}


