<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class UsedCar extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'auto_ru',
        'showroom_id',
        'income_date',
        'car_name',
        'mark_id',
        'model_id',
        'year',
        'bodytype_id',
        'transmission_id',
        'enginetype_id',
        'wheeltype_id',
        'owner_count',
        'power',
        'salon',
        'color',
        'milage',
        'volume',
        'vin_number',
        'number',
        'sts',
        'ptc',
        'income_price',
        'price',
        'price_avito',
        'key_number',
        'status_id',
        'registered',
        'transit',
        'manager',
        'comment_purchase',
        'comment',
        'pictures',
        'pictures2',
        'videos',
        'expose',
        'sale_autoru',
        'is_ready',
        'manager_id',
        'is_tradein',
        'deleted_at',
        'diagnostic_before',
        'diagnostic_after',
        'dry_cleaning',
        'polish',
        'painting',
        'repair_price',
        'painting_price',
        'transport_price',
        'preparation_price',
        'frontal_price',
        'milage_price',
        'removal_price',
        'user_id',
        'repair',
        'entity',
        'sale_repeat',
        'repair_body',
        'repair_painting',
        'repair_locksmith',
        'repair_electric',
        'repair_others',
        'repair_dry',
        'repair_polishing',
        'repair_milage',
        'repair_windshield',
        'body_status',
        'painting_status',
        'locksmith_status',
        'electric_status',
        'others_status',
        'dry_cleaning_status',
        'polishing_status',
        'milage_status',
        'windshield_status',
        'preparation_status',
        'body_comment',
        'painting_comment',
        'locksmith_comment',
        'electric_comment',
        'others_comment',
        'dry_cleaning_comment',
        'polishing_comment',
        'millage_comment',
        'windshield_comment',
        'preparation_comment',
    ];
    protected $casts = [
        'pictures' => 'array',
        'pictures2' => 'array',
        'videos' => 'array',
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    public static  $submitEmptyLogs = false;

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->causer_id = Auth::id();
        $activity->subject_type = 4;
        if ($eventName === 'updated'){
            $activity->description = 3;
        }
        if ($eventName === 'deleted'){
            $activity->description = 4;
        }
        if ($eventName === 'created'){
            $activity->description = 1;
        }
        $activity = $this->removeEmptyFields($activity);
    }

    function removeEmptyFields($object) {
        $filteredObject = (object) array_filter((array) $object, function ($value) {
            // This callback filters out empty, null, and false values
            return !empty($value) || $value === 0 || $value === '0';
        });
        // Convert the filtered array back to an object
        return (object) $filteredObject;
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

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager');
    }

    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }

    public function transits()
    {
        return $this->hasMany('App\Models\UsedcarTransit', "usedcar_id");
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


}
