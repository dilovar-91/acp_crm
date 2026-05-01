<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShipmentDetail extends Model
{
    use HasFactory;

    public function shipment(): BelongsTo
    {
        return $this->belongsTo('App\Models\Shipment');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo('App\Models\Car')->where('is_used', '<>', 1);
    }

    public function used_car(): BelongsTo
    {
        return $this->belongsTo('App\Models\UsedCar')->where('is_used', '=', 1);
    }


}
