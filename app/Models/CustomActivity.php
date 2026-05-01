<?php
namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class CustomActivity extends Activity
{
    public function getCauserTypeAttribute($value)
    {
        // Map your class names to numerals here
        $mapping = [
            'App\Models\User' => 1,
            // Add more mappings as needed
        ];

        return $mapping[$value] ?? $value;
    }

    public function getSubjectTypeAttribute($value): int
    {

        // Map your class names to numerals here
        $mapping = [
            'App\Models\User' => 1,
            'App\Models\Order' => 2,
            'App\Models\Car' => 3,
            'App\Models\UsedCar' => 4,
            'App\Models\Site' => 5,
            // Add more mappings as needed
        ];

        return $mapping[$value] ?? $value;
    }

    public function getDescriptionForEvent(string $eventName): string
    {

        if ($eventName === 'updated') {
            return 3; // Set your desired number or custom description here
        } else if ($eventName === 'created') {
            return 1; // Set your desired number or custom description here
        }

        return parent::getDescriptionForEvent($eventName);
    }
}
