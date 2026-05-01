<?php

namespace App\Models;

use App\Helpers\GeneralHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Throwable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity as ActivityLog;

class Order extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'id',
        'client_name',
        'phone',
        'phone_2',
        'phone_3',
        'work_phone',
        'callback',
        'type_id',
        'mark_id',
        'model_id',
        'comment',
        'general_comment',
        'operator_id',
        'birthday',
        'car_year',
        'country',
        'region_id',
        'price',
        'complectation',
        'initial_fee',
        'credit_period',
        'month_pay',
        'showroom_id',
        'payment_method',
        'last_call',
        'will_arrive',
        'arrived_date',
        'arrived',
        'status_id',
        'site_id',
        'date_of_sale',
        'call_heard',
        'commercial_offer',
        'approved',
        'canceled',
        'link_1',
        'link_2',
        'trash_id',
        'drop_id',
        'arrival_id',
        'call_heard',
        'official_income',
        'supouse_name'
    ];


    protected static $logFillable = true;

    protected static $logOnlyDirty = true;

    public static $submitEmptyLogs = false;

    protected static $ignoreChangedAttributes = ['updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->disableLogging();
        });

        static::created(function ($model) {
            $model->enableLogging();
        });
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')->where('subject_type', Order::class);
    }

    public function deleteOrder()
    {
        if (isset($this)) {
            $this->activityLogs()->delete();
            $this->delete();
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = parent::getActivitylogOptions();

        $logOptions->disableLoggingForEvents(['created']);

        return $logOptions;
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check() && auth()->user()->id === 4) {
            // User has role_id 1, don't log the activity
            return;
        }

        $activity->causer_id = Auth::id();
        $activity->subject_type = 2;
        if ($eventName === 'updated') {
            $activity->description = 3;
        }
        if ($eventName === 'deleted') {
            $activity->description = 4;
        }
        if ($eventName === 'created') {
            $activity->description = 1;
        }
    }

    //passed ao to victory
    public function passedOrder()
    {
        return $this->hasOne(PassedOrder::class, 'order_id');
    }

    public function getSubjectType()
    {
        return $this->getKey();
    }

    protected $casts = [
        'history' => 'array',
        'copied_to' => 'array',
    ];
    //protected $table = 'watches';
    /**
     * @var mixed
     */
    private $client_name;

    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\OrderType');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus');
    }

    public function missed_call()
    {
        return $this->hasOne('App\Models\MissedCall');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    public function mark()
    {
        return $this->belongsTo('App\Models\Brand');
    }

    public function source()
    {
        return $this->belongsTo('App\Models\OrderSource');
    }

    public function model()
    {
        return $this->belongsTo('App\Models\CarModel');
    }

    public function trash()
    {
        return $this->belongsTo('App\Models\OrderTrash');
    }

    public function arrival_status()
    {
        return $this->belongsTo('App\Models\OrderArrival', 'arrival_id');
    }

    public function scopeAgency($query, $relationId)
    {
        return $query->whereHas('site', function ($query) use ($relationId) {
            $query->where('agency_id', $relationId);
        });
    }

    public function scopeUtm($query, string $utm)
    {
        $utm = trim($utm);

        if ($utm === '') {
            return $query;
        }

        // Экранируем LIKE-символы (% и _), чтобы искалось именно значение
        $like = '%' . addcslashes($utm, '%_\\') . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('utm_campaign', 'LIKE', $like)
                ->orWhere('utm_content',  'LIKE', $like)
                ->orWhere('utm_term',     'LIKE', $like)
                ->orWhere('utm_source',   'LIKE', $like)
                ->orWhere('utm_medium',   'LIKE', $like);
        });
    }

    //scopes
    public function scopeBetween($query, $date1, $date2)
    {
        //Log1::debug($date1. " " .$date2);
        return $query->whereBetween('created_at', [$date1, $date2]);
    }

    public function scopeBetweenUpdated($query, $date1, $date2)
    {
        return $query->whereBetween('updated_at', [$date1, $date2]);
    }

    public function scopeNotConfirmed($query, $value)
    {
        if ($value) {
            $query->whereNull('arrived') // Check if the column is NULL
                ->orWhere('arrived', '!=', true);
        } else return $query;
    }

    public function scopeToday($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        return $query->whereDate('created_at', $today)->orWhereDate('will_arrive', $today)->orWhereDate('callback', $today)->orWhereIn('status_id', [1, 2]);
    }

    public function scopePaymentUndefined($query, $value)
    {
        if ($value === true) {
            return $query->whereIn('payment_method', [0, 7]);
        }
    }


    public function scopeApprovedCredit($query, $value)
    {
        if ($value === true) {
            return $query->where('payment_method', 2)->where('approved', 1);
        }
    }


    public function scopeCreditCount($query, $value)
    {
        if ($value === true) {
            return $query->where('payment_method', 2);
        }
    }

    public function scopeRepeat($query, $value)
    {
        if ($value === true) {
            return $query->orderBy('retries', 'DESC');
        }
    }

    public function scopeShowroom($query, $showroom_id)
    {
        $query->where('showroom_id', $showroom_id);
    }

    public function scopeBetweenArrive($query, $date1, $date2)
    {
        return $query->whereBetween('will_arrive', [$date1 . ':00', $date2 . '59']);
    }

    public function scopeBetweenArrived($query, $date1, $date2)
    {
        return $query->whereBetween('arrived_date', [$date1, $date2]);
    }

    public function scopeBetweenCallback($query, $date1, $date2)
    {
        return $query->whereBetween('callback', [$date1, $date2]);
    }

    public function scopeLastCall($query, $date1, $date2)
    {
        return $query->whereBetween('last_call', [$date1, $date2]);
    }

    public function scopeSearch($query, $searchTerm)
    {
        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone = preg_replace('/\D/', '', $searchTerm);
            }
        } else if (preg_match('/\d/', $searchTerm)) {
            $phone = preg_replace('/\D/', '', $searchTerm);
        } else {
            $phone = $searchTerm;
        }

        $phone_digit = "not digit";
        $stripped_string = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($stripped_string) > 2) {
            $phone_digit = $stripped_string;
        }
        $digits = str_replace(["+", "-", " ", "(", ")"], "", $searchTerm);
        $last_digits = substr($digits, -4);
        return $query
            ->where('client_name', 'like', "%" . $searchTerm . "%")
            ->where('client_name', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_2', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_2', 'like', "%" . $phone . "%")
            ->orWhere('phone_3', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_3', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone', 'like', "%" . $digits . "%")
            ->orWhere('phone', $phone)
            ->orWhere('phone', "+" . $phone)
            ->orWhere('phone_2', $phone)
            ->orWhere('phone_2', "+" . $phone)
            ->orWhere('phone_3', $phone)
            ->orWhere('phone_3', "+" . $phone)
            ->orWhere('phone', $phone_digit)
            ->orWhere('work_phone', "+" . $phone_digit)
            ->orWhere('work_phone', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $digits . "%")
            ->orWhere('work_phone', $phone)
            ->orWhere('work_phone', "+" . $phone)
            ->orWhere('work_phone', $phone_digit)
            ->orWhere('work_phone', 'like', "%" . $digits . "%")
            ->orderBy(DB::raw('RIGHT(phone, 4) = "' . substr($last_digits, -4) . '"'), 'desc')
            ->orderByRaw('CHAR_LENGTH(phone)');
    }


    public function scopeSearch2($query, $searchTerm)
    {
        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone = preg_replace('/\D/', '', $searchTerm);
            }
        } else if (preg_match('/\d/', $searchTerm)) {
            $phone = preg_replace('/\D/', '', $searchTerm);
        } else {
            $phone = $searchTerm;
        }

        $phone_digit = "not digit";
        $stripped_string = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($stripped_string) > 2) {
            $phone_digit = $stripped_string;
        }
        $digits = str_replace(["+", "-", " ", "(", ")"], "", $searchTerm);
        $last_digits = substr($digits, -4);
        return $query
            ->where('client_name', 'like', "%" . $searchTerm . "%")
            ->where('client_name', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_2', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_2', 'like', "%" . $phone . "%")
            ->orWhere('phone_3', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_3', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone', 'like', "%" . $digits . "%")
            ->orWhere('phone', $phone)
            ->orWhere('phone', "+" . $phone)
            ->orWhere('phone_2', $phone)
            ->orWhere('phone_2', "+" . $phone)
            ->orWhere('phone_3', $phone)
            ->orWhere('phone_3', "+" . $phone)
            ->orWhere('phone', $phone_digit)
            ->orWhere('work_phone', "+" . $phone_digit)
            ->orWhere('work_phone', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $digits . "%")
            ->orWhere('work_phone', $phone)
            ->orWhere('work_phone', "+" . $phone)
            ->orWhere('work_phone', $phone_digit)
            ->orWhere('work_phone', 'like', "%" . $digits . "%")
            ->orderByRaw("CASE WHEN status_id = 2 THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc');
    }

    public function scopeSearchPhone($query, $searchTerm)
    {
        $phone = null;

        // Определяем телефон, если это номер
        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone = preg_replace('/\D/', '', $searchTerm);
            }
        } elseif (preg_match('/\d/', $searchTerm)) {
            $phone = preg_replace('/\D/', '', $searchTerm);
        }

        $searchPatterns = [
            $phone . "%"
        ];

        $query->where(function ($q) use ($searchTerm, $phone, $searchPatterns) {
            $phoneFields = ['phone', 'phone_2', 'phone_3', 'work_phone'];
            foreach ($phoneFields as $field) {
                $q->orWhere(function ($q) use ($field, $searchPatterns, $phone) {
                    $q->where($field, 'like', $searchPatterns[0])
                        ->orWhere($field, $phone)
                        ->orWhere($field, "+" . $phone);
                });
            }
        });

        // Сортировка по статусу и дате создания
        return $query->orderByRaw("CASE WHEN status_id = 2 THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc');
    }

    // not sorted scope
    public function scopeFindPhone($query, $searchTerm)
    {
        $phone = null;

        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone = preg_replace('/\D/', '', $searchTerm);
            }
        } elseif (preg_match('/\d/', $searchTerm)) {
            $phone = preg_replace('/\D/', '', $searchTerm);
        }

        $searchPatterns = [
            $phone . "%"
        ];

        return $query->where(function ($q) use ($searchTerm, $phone, $searchPatterns) {
            $phoneFields = ['phone', 'phone_2', 'phone_3', 'work_phone'];
            foreach ($phoneFields as $field) {
                $q->orWhere(function ($q) use ($field, $searchPatterns, $phone) {
                    $q->where($field, 'like', $searchPatterns[0])
                        ->orWhere($field, $phone)
                        ->orWhere($field, "+" . $phone);
                });
            }
        });
    }


    public function scopeSearchPhonePv($query, $searchTerm)
    {
        try {
            $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
        } catch (Throwable $e) {
            $phone = preg_replace('/\D/', '', $searchTerm);
        }

        $fields = ['phone', 'phone_2', 'phone_3', 'work_phone'];
        return $query->where(function ($q) use ($phone, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%" . $phone . "%");
            }
        });
    }


    public function scopePhone($query, $phone)
    {
        return $query->where(function ($query) use ($phone) {
            $query->where('phone', 'LIKE', '%' . $phone . '%')
                ->orWhere('phone_2', 'LIKE', '%' . $phone . '%')
                ->orWhere('phone_3', 'LIKE', '%' . $phone . '%')
                ->orWhere('work_phone', 'LIKE', '%' . $phone . '%')->orderBy('updated_at', 'DESC')->orderBy('created_at', 'DESC');
        });
    }


    public function scopePhone2($query, $searchTerm)
    {
        try {
            $phone = preg_replace('/[^0-9+]/', '', $searchTerm);;;
        } catch (Throwable $e) {
            $phone = preg_replace('/[^0-9+]/', '', $searchTerm);
        }

        return $query
            ->where('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone_2', 'like', "%" . $phone . "%")
            ->orWhere('phone_3', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $phone . "%")
            ->orWhere('phone', $phone)
            ->orWhere('phone', "+" . $phone)
            ->orWhere('phone_2', $phone)
            ->orWhere('phone_2', "+" . $phone)
            ->orWhere('phone_3', $phone)
            ->orWhere('phone_3', "+" . $phone)
            ->orWhere('work_phone', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', $phone)
            ->orWhere('work_phone', "+" . $phone)
            ->orderBy('updated_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->orderBy('retries', 'asc')
            ->orderByRaw("
                CASE
                    WHEN status_id = 9 THEN 1
                    WHEN status_id = 2 THEN 2
                    WHEN status_id = 12 THEN 3
                    WHEN status_id = 4 THEN 4
                    WHEN status_id = 5 THEN 5
                    WHEN status_id = 6 THEN 6
                    WHEN status_id = 1 THEN 7
                    ELSE 8
                END
            ");
    }


    public function scopePhone3($query, $phone)
    {
        //Log::warning($phone);
        $fields = ['phone', 'phone_2', 'phone_3', 'work_phone'];
        return $query->where(function ($q) use ($phone, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%" . $phone . "%");
            }
        })->orderByRaw("
            CASE
                WHEN status_id = 9 THEN 1
                WHEN status_id = 2 THEN 2
                WHEN status_id = 12 THEN 3
                WHEN status_id = 4 THEN 4
                WHEN status_id = 5 THEN 5
                WHEN status_id = 6 THEN 6
                WHEN status_id = 1 THEN 7
                ELSE 8
            END
        ");
    }


    public function scopePhone4($query, $phone)
    {
        //Log::warning($phone);
        $fields = ['phone', 'phone_2', 'phone_3', 'work_phone'];
        return $query->where(function ($q) use ($phone, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%" . $phone);
            }
        })->orderByRaw("
            CASE
                WHEN status_id = 9 THEN 1
                WHEN status_id = 2 THEN 2
                WHEN status_id = 12 THEN 3
                WHEN status_id = 4 THEN 4
                WHEN status_id = 5 THEN 5
                WHEN status_id = 6 THEN 6
                WHEN status_id = 1 THEN 7
                ELSE 8
            END
        ");
    }


    public function scopeTell($query, $phone)
    {
        return $query
            ->where('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone_2', 'like', "%" . $phone . "%")
            ->orWhere('phone_3', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $phone . "%")->orderBy('updated_at', 'DESC')->orderBy('created_at', 'DESC');
    }


    public function scopeToShowroom($query, $showroom_ids)
    {
        $array = explode(",", $showroom_ids);
        return $query->whereHas('site', function ($query) use ($array) {
            $query->whereIn('to_showroom', $array);
        });
    }


    public function scopeArrivedType($query, $arrival_id)
    {
        return $query->where('arrival_id', $arrival_id);
    }


    public function scopeSearchFion($query, $searchTerm)
    {
        $parts = explode(" ", $searchTerm);
        if (count($parts) >= 2 && preg_match('/\d/', $searchTerm) != true) {
            $query->where('comment', 'LIKE', '%' . $searchTerm . '%');
            // Generate and add additional conditions with orWhere
            for ($i = 0; $i < count($parts); $i++) {
                $combination = implode(" ", array_merge(array_slice($parts, $i), array_slice($parts, 0, $i)));
                $query->orWhere('client_name', 'LIKE', '%' . preg_replace('/\s+/', ' ', $combination) . '%');
            }
            return $query;
        } else {
            return $query->where('client_name', 'LIKE', '%' . $searchTerm . '%')->orWhere('comment', 'LIKE', '%' . $searchTerm . '%');
        }
    }


    public function scopeSearchFio($query, $searchTerm)
    {
        $parts = explode(" ", $searchTerm);
        $combinations = [];

        // Генерируем все возможные комбинации
        for ($i = 0; $i < count($parts); $i++) {
            $combination = $parts[$i];
            for ($j = ($i + 1) % count($parts); $j != $i; $j = ($j + 1) % count($parts)) {
                $combination .= " " . $parts[$j];
                $combinations[] = preg_replace('/\s+/', ' ', $combination);
            }
        }

        // Выполняем запрос с условием, что клиентское имя содержит любую из комбинаций
        $query->where('comment', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere(function ($query) use ($combinations) {
                foreach ($combinations as $combination) {
                    $query->orWhere('client_name', 'LIKE', '%' . $combination . '%');
                }
            })->orderByRaw("CASE
                              WHEN client_name LIKE '%$searchTerm%' THEN 1
                              WHEN client_name LIKE '%" . implode("%' THEN 2 WHEN client_name LIKE '%", $combinations) . "%' THEN 3
                              ELSE 4
                        END");

        return $query;
    }

    public function scopeSearchBest($query, $searchTerm)
    {
        $parts = explode(" ", $searchTerm);

        if (count($parts) >= 2 && !preg_match('/\d/', $searchTerm)) {
            return $query->where(function ($query) use ($parts) {
                foreach ($parts as $part) {
                    $query->orWhere('client_name', 'LIKE', '%' . $part . '%');
                }
            });
        } else {
            return $query->where(function ($query) use ($searchTerm) {
                $query->where('client_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('comment', 'LIKE', '%' . $searchTerm . '%');
            });
        }
    }

    public function scopeSearchClient($query, $searchTerm)
    {
        return $query->where('client_name', 'LIKE', '%' . $searchTerm . '%');
    }

    public function scopeSearchComment($query, $searchTerm)
    {
        return $query->where('comment', 'LIKE', '%' . $searchTerm . '%');
    }

    public function scopeBlacklist($query, $searchTerm)
    {
        return $query->whereHas('blacklist');
    }


    public function scopeSearchTelephone($query, $searchTerm)
    {
        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone = preg_replace('/[^0-9+]/', '', $searchTerm);
            }
        } else $phone = $searchTerm;
        $phone_digit = "not digit";
        $stripped_string = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($stripped_string) > 2) {
            $phone_digit = $stripped_string;
        }
        $digits = str_replace(["+", "-", " ", "(", ")"], "", $searchTerm);
        $last_digits = substr($digits, -4);
        return $query
            ->where('phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_2', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_3', 'like', "%" . $searchTerm . "%")
            ->orWhere('work_phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone', 'like', "%" . $digits . "%")
            ->orWhere('phone', $phone)
            ->orWhere('phone', "+" . $phone)
            ->orWhere('phone_2', $phone)
            ->orWhere('phone_2', "+" . $phone)
            ->orWhere('phone_3', $phone)
            ->orWhere('phone_3', "+" . $phone)
            ->orWhere('phone', $phone_digit)
            ->orWhere('work_phone', "+" . $phone_digit)
            ->orWhere('work_phone', 'like', "%" . $phone . "%")
            ->orWhere('work_phone', 'like', "%" . $digits . "%")
            ->orWhere('work_phone', $phone)
            ->orWhere('work_phone', "+" . $phone)
            ->orWhere('work_phone', $phone_digit)
            ->orderBy(DB::raw('RIGHT(phone, 4) = "' . substr($last_digits, -4) . '"'), 'desc')
            ->orderByRaw('CHAR_LENGTH(phone)');
    }


    public function blacklist()
    {
        return $this->hasOne(BlacklistOrder::class);
    }


    public function scopeDeferredPurchase($query)
    {
        return $query->whereHas('deferredPurchases');
    }

    public function scopeDeferredReturnDate($query, $date1, $date2)
    {
        return $query->whereHas('deferredPurchases', function ($q) use ($date1, $date2) {
            $q->whereBetween('return_date', [$date1, $date2]);
        });
    }

    public function scopeReturned($query)
    {
        return $query->whereHas('deferredPurchases', function ($q) {
            $q->where('returned', true);
        });
    }


    public function deferredPurchases()
    {
        return $this->hasMany(DeferredPurchase::class);
    }

    public function deferPurchase()
    {
        return $this->hasOne(DeferredPurchase::class);
    }
}
