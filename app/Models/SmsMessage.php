<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    protected $table = 'sms_messages';

    protected $fillable = [
        'showroom_id','phone','text','sender','schedule_time_utc','status_queue_name',
        'provider_message_id','status','error_code','error_message','provider_response',
        'sent_at','delivered_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'schedule_time_utc' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
