<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public function scopeMark(Builder $query, $mark)
    {
        return $query->whereRelation('car', 'mark_id', '=', $mark);
    }

    public function from_showroom(){
        return $this->belongsTo('App\Models\Showroom', 'from');
    }

    public function showroom(){
        return $this->belongsTo('App\Models\Showroom');
    }

    public function driver(){
        return $this->belongsTo('App\Models\Driver');
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User', "sender_id");
    }
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', "receiver_id");
    }
    public function status()
    {
        return $this->belongsTo('App\Models\ShipmentStatus', "status_id");
    }

    public function detail(): HasMany
    {
        return $this->hasMany('App\Models\ShipmentDetail');
    }

    // Inside your User model
    public function cars() {
        return $this->hasMany('App\Models\ShipmentCar');
    }
    // Inside your User model
    public function used_cars() {
        return $this->hasMany('App\Models\ShipmentUsedCar');
    }



}
