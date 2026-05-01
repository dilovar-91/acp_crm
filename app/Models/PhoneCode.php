<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhoneRegion;
use App\Models\PhoneOperator;

class PhoneCode extends Model
{
    use HasFactory;

    public function region()
    {
        return $this->belongsTo(PhoneRegion::class,  'region_id');
    }

    public function operator()
    {
        return $this->belongsTo(PhoneOperator::class,  'operator_id');
    }
}
