<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastRequest extends Model
{
    use HasFactory;
    protected $table = 'last_request_time';
    protected $dates = ['last_request_time'];

    public function site()
    {
        return $this->belongsTo('App\Models\Site');
    }
}
