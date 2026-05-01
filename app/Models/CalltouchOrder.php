<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalltouchOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'order_id',
        'status',
        'sent_at',
    ];
}
