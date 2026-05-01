<?php

namespace App\Helpers;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Support\Facades\Log;

/**
 * Class GeneralHelper.
 */
class GeneralHelper
{
    public static function isValidTelephoneNumber(string $telephone, int $minDigits = 9, int $maxDigits = 14): bool {
        if (preg_match('/^[+][0-9]/', $telephone)) { //is the first character + followed by a digit
            $count = 1;
            $telephone = str_replace(['+'], '', $telephone, $count); //remove +
        }
        //remove white space, dots, hyphens and brackets
        $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);
        //are we left with digits only?
        return preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $telephone);
    }

    public static function phone_format($phone)
    {
        $phone = trim($phone);

        $res = preg_replace(
            array(
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            ),
            array(
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4',
                '+7 ($2) $3-$4',
            ),
            $phone
        );

        return $res;
    }


    public static function getShowroomKeyByValue($value)
    {
        $showroomPairs = [
            10 => 18,
            17 => 20,
            15 => 21,
            5 => 22,
            4 => 24,
            2 => 25,

        ];

        $key = array_search($value, $showroomPairs);

        if ($key !== false) {
            return $key;
        } else {
            Log::error('Showroom not found on getShowroomKeyByValue');
            return null;
        }
    }



    public static function getType($type)
    {
        if (preg_match('/used_reserve/', $type)) {
            return 2;
        } else if (preg_match('/used_reserve/', $type)) {
            return 11;
        } else if (preg_match('/Зарезервировать/', $type)) {
            return 11;
        } else if (preg_match('/Остались вопросы/', $type)) {
            return 13;
        } else return 5;
    }


    public static function getCar($brand, $model)
    {
        if ($brand === "") return;
        $mark = Brand::where('name', 'like', '%' . $brand . '%')->first();
        if (!empty($mark)) {
            $model = CarModel::search($model)->where('brand_id', $mark->id)->first();
        } else {
            $model = CarModel::search($model)->first();
        }

        return $model;
    }


    public static function getNextShowroomId($currentIndex, $showrooms): int
    {
        if (isset($showrooms[$currentIndex])) {
            return (int) $showrooms[$currentIndex];
        }

        return (int) reset($showrooms);
    }



    public static function getShowroomGroup($showroom_id) {
        $groups = [
            'rostov' => [10, 4, 2],
            'krasnodar' => [5, 8],
            'simferopol' => [15, 17],
        ];
        foreach ($groups as $groupName => $ids) {
            if (in_array($showroom_id, $ids)) {
                return $groupName;
            }
        }
        return null;
    }
}
