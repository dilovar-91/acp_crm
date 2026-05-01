<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_log';
    protected $casts = [
        'properties' => 'array',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function record()
    {
        return $this->hasOne(ActivityLog::class, 'entry_id', 'entry_id')->where('description', 8);
    }

    public function scopeCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeMark(Builder $query, $mark): Builder
    {
        return $query->whereJsonContains('properties->attributes', ['mark_id' => intval($mark)]);
    }

    public function scopeModel(Builder $query, $model): Builder
    {
        return $query->whereJsonContains('properties->attributes', ['model_id' => intval($model)]);
    }

    public function scopeShowroom(Builder $query, $value): Builder
    {
        return $query->whereJsonContains('properties', ['showroom_id' => intval($value)]);
    }

}
