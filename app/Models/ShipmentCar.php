<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCar extends Model
{
    use HasFactory;


    public function car(): BelongsTo
    {
        return $this->belongsTo('App\Models\Car');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo('App\Models\Shipment');
    }
}
