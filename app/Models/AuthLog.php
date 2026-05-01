<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;
class AuthLog extends Model
{
    use HasFactory;

    protected $table = 'authentication_log';

    protected $casts = [
        'location' => 'array',
    ];

    public function getUserAgentAttribute($userAgent)
    {
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        return ['browser'=>$agent->browser(), 'device'=>$agent->device(), 'platform'=>$agent->platform()];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authenticatable_id');
    }

    public function scopeBetween($query, $date1, $date2)
    {
        //Log1::debug($date1. " " .$date2);
        return $query->whereBetween('login_at', [$date1, $date2])->orWhere('logout_at', [$date1, $date2]);
    }

    public function scopeByShowroomId($query, $showroomId)
    {
        if (!empty($showroomId)) {
            return $query->whereHas('user', function ($query) use ($showroomId) {
                $query->where('showroom_id', $showroomId);
            });
        } else return $query;
    }



}
