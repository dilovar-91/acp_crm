<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CreditRequest extends Model
{
    use SoftDeletes;


    protected $hidden = ['updated_at'];

    protected $fillable = ['id',
    'date',
    'client_name',
    'region_id',
    'live_region',
    'mark_id',
    'model',
    'showroom_id',
    'site_id',
    'phone_number',
    'price',
    'initial_fee',
    'manager_id',
    'manager2_id',
    'operator_id',
    'comments',
    'sd_comment',
    'absolute',
    'expo',
    'sovkom',
    'oranzh',
    'tinkoff',
    'kvant',
    'zenit',
    'loco_bank',
    'cetelem',
    'vtb',
    'keb',
    'rnb',
    'uralsib',
    'primsots',
    'otp_bank',
    'alfa_bank',
    'pochta_bank',
    'bistro_bank',
    'atb',
    'absolute_manager',
    'expo_manager',
    'sovkom_manager',
    'oranzh_manager',
    'tinkoff_manager',
    'kvant_manager',
    'zenit_manager',
    'loco_manager',
    'cetelem_manager',
    'vtb_manager',
    'keb_manager',
    'rnb_manager',
    'uralsib_manager',
    'primsots_manager',
    'otp_bank_manager',
    'alfa_bank_manager',
    'atb_manager',
    'pochta_bank_manager',
    'bistro_bank_manager',
    'otkritie',
    'otkritie_manager',
    'canceled',
    'client_canceled',
    'fssp_canceled',
    'jump',
    'transit',
    'transit_confirmed',
    'car_name',
    'sale_date',
    'sale_bank_id',
    'sale_vin',
    'is_sold',
    'is_tradein',
    'sale_manager_id',
    'sale_creditor_id',
    'sale_oformitel_id',
    'sale_source',
    'sale_repeat',
    'is_sale',
    'source_credit',
    'pictures',
    'pictures2',
    'videos',
    'created_at',
    'updated_at',
    'deleted_at',
    'sale_call_date',
    'sale_recall_date',
    'sale_vote_id',
    'sale_control',
    'sale_comment',
    'co_applicant',
    'co_applicant_name',
    'parent_id'];

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    public function region_live()
    {
        return $this->belongsTo('App\Models\Region', "live_region");
    }

    public function mark()
    {
        return $this->belongsTo('App\Models\Brand');

    }

    public function operator()
    {
        return $this->belongsTo('App\Models\Operator');
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function manager2()
    {
        return $this->belongsTo('App\Models\User', 'manager2_id');
    }

    public function sale_manager()
    {
        return $this->belongsTo('App\Models\User', 'sale_manager_id');
    }

    public function sale_creditor()
    {
        return $this->belongsTo('App\Models\User', 'sale_creditor_id');
    }

    public function oformitel()
    {
        return $this->belongsTo('App\Models\User', 'sale_oformitel_id');
    }

    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'sale_bank_id');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }

    public function parent()
    {
        return $this->hasOne(CreditRequest::class, 'id', 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(CreditRequest::class, 'parent_id', 'id');
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query
            ->where('client_name', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone_number', 'like', "%" . $searchTerm . "%")
            ->orWhere('comments', 'like', "%" . $searchTerm . "%")
            ->orWhere('sd_comment', 'like', "%" . $searchTerm . "%");
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeSaleBetween($query, $from, $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }
}
