<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistOrder extends Model
{
    protected $fillable = ['order_id', 'reason', 'status_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
