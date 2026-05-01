<?php

namespace App\Models;

use App\Helpers\GeneralHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class Blacklist extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'showroom_id', 'comment'];
    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }


    public function scopeSearch($query, $searchTerm)
    {
        if (GeneralHelper::isValidTelephoneNumber($searchTerm ?? "", 11, 11)) {
            try {
                $phone = PhoneNumber::make($searchTerm, 'RU')->formatE164();
            } catch (Throwable $e) {
                $phone =  preg_replace('/[^0-9+]/', '', $searchTerm);


            }
        } else $phone = $searchTerm;
        $phone_digit = "not digit";
        $stripped_string = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($stripped_string) > 2) {
            $phone_digit = $stripped_string;
        }
        $digits = str_replace(["+", "-", " ", "(", ")"], "", $searchTerm);

        return $query
            ->where('phone', 'like', "%" . $searchTerm . "%")
            ->orWhere('phone', 'like', "%" . $phone . "%")
            ->orWhere('phone', 'like', "%" . $digits . "%")
            ->orWhere('phone', 'like', "%" . $phone_digit . "%");
    }
}
