<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentUsedCar extends Model
{
    use HasFactory;

    public function car()
    {
        return $this->belongsTo('App\Models\UsedCar', 'car_id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo('App\Models\Shipment');
    }
}
