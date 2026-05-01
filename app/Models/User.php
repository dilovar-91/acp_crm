<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity, Loggable, HasRolesAndAbilities, AuthenticationLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role_id',
        'showroom_id',
        'work_place',
        'active',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'code',
        'created_at',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];


    protected static $logAttributes = ['name'];

    /*
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }*/
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'operator_id');
    }


    public function schedule(): HasOne
    {
        return $this->hasOne(OperatorSchedule::class, 'user_id');
    }


    public function operatorSchedule(): HasOne
    {
        return $this->hasOne(OperatorSchedule::class, 'user_id');
    }


    public function programmaticSchedule(): HasOne
    {
        return $this->hasOne(ProgrammaticSchedule::class, 'user_id');
    }

    /**
     * Scope to sort users based on whether they are working today.
     */
    public function scopeOrderWorkers($query)
    {
        $now = Carbon::now();
        $today = $now->englishDayOfWeek;
        $startOfEvening = $now->copy()->setTime(19, 0);
        $endOfDay = $now->copy()->setTime(23, 59);
        if ($now->between($startOfEvening, $endOfDay)) {
            $today = $now->addDay()->englishDayOfWeek;
        }
        $today = Str::lower($today);
        return $query->with('schedule')
            ->orderByRaw("CASE WHEN EXISTS (
                SELECT 1 FROM operator_schedules WHERE operator_schedules.user_id = users.id AND operator_schedules.$today = 1
            ) THEN 0 ELSE 1 END");
    }


}
