<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MangoCallRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'mango_call_id',
        'mango_account_id',
        'entry_id',
        'recording_id',
        'user_id',
        'recorded_at',
        'attached_at',
        'payload',
    ];

    protected $casts = [
        'recorded_at' => 'integer',
        'attached_at' => 'datetime',
        'payload' => 'array',
    ];

    public function call(): BelongsTo
    {
        return $this->belongsTo(MangoCall::class, 'mango_call_id');
    }
}
