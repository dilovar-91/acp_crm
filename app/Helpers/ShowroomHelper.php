<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ShowroomHelper
{
    protected static $showroomPairs = [
        10 => 18,
        17 => 20,
        15 => 21,
        5 => 22,
        4 => 24,
        2 => 25,
    ];

    public static function getShowroomPair($showroomId)
    {
        if (array_key_exists($showroomId, self::$showroomPairs)) {
            return self::$showroomPairs[$showroomId];
        } else {
            Log::error('Showroom not found on getShowroomPair');
            return null;
        }
    }

    public static function getShowroomKeyByValue($value)
    {
        $key = array_search($value, self::$showroomPairs);

        if ($key !== false) {
            return $key;
        } else {
            Log::error('Showroom not found on getShowroomKeyByValue');
            return null;
        }
    }
}
