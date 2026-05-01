<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $table = 'sms_templates';

    protected $fillable = [
        'showroom_id',
        'operator_id',
        'name',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // если есть модель Showroom
    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }
}
