<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        //'income_date' => 'datetime:d-m-Y',
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->causer_id = Auth::id();
        $activity->subject_type = 3;
        if ($eventName === 'updated'){
            $activity->description = 3;
        }
        if ($eventName === 'deleted'){
            $activity->description = 4;
        }
        if ($eventName === 'created'){
            $activity->description = 1;
        }
    }

    public function complectation()
    {
        return $this->belongsTo('App\Models\Complectation');
    }

    public function mark()
    {
        return $this->belongsTo('App\Models\Brand');

    }
    public function model()
    {
        return $this->belongsTo('App\Models\CarModel');

    }
    public function bodytype()
    {
        return $this->belongsTo('App\Models\BodyType');

    }
    public function transmission()
    {
        return $this->belongsTo('App\Models\Transmission');

    }

    public function color()
    {
        return $this->belongsTo('App\Models\Color');

    }
    public function enginetype()
    {
        return $this->belongsTo('App\Models\EngineType');

    }
    public function wheeltype()
    {
        return $this->belongsTo('App\Models\WheelType');

    }
    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }
    public function transits()
    {
        return $this->hasMany('App\Models\Transit', 'car_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status');
    }

}
