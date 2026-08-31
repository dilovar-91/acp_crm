<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MangoCall extends Model
{
    use HasFactory;

    public const DIRECTION_INTERNAL = 0;
    public const DIRECTION_INCOMING = 1;
    public const DIRECTION_OUTGOING = 2;

    protected $fillable = [
        'mango_account_id',
        'entry_id',
        'showroom_id',
        'order_id',
        'site_id',
        'operator_id',
        'direction',
        'client_phone',
        'line_number',
        'operator_extension',
        'status',
        'entry_result',
        'disconnect_reason',
        'create_time',
        'talk_time',
        'end_time',
        'call_sequences',
        'popup_sent',
        'summary_processed',
        'payload',
    ];

    protected $casts = [
        'direction' => 'integer',
        'entry_result' => 'integer',
        'disconnect_reason' => 'integer',
        'create_time' => 'integer',
        'talk_time' => 'integer',
        'end_time' => 'integer',
        'call_sequences' => 'array',
        'popup_sent' => 'boolean',
        'summary_processed' => 'boolean',
        'payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(MangoCallRecording::class);
    }
}
