<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'phone',
        'second_phone',
        'url',
        'pic',
        'description',
        'showroom_id',
        'to_showroom',
        'type_id',
        'agency_id',
        'manager_id',
        'active',
        'actual',
        'order',
        'token',
        'post_url',
        'import',
        'calltouch_access_key',
        'calltouch_site_id',
        'mango_site_id',
        'mango_token',
        'sip'
    ];


    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
    }

    public function request()
    {
        return $this->hasOne('App\Models\LastRequest');
    }
}
