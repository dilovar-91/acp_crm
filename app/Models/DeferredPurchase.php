<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeferredPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'return_date', 'returned'];
}
