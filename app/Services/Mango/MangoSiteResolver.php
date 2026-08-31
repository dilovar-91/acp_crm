<?php

namespace App\Services\Mango;

use App\Models\Site;
use Illuminate\Support\Str;

class MangoSiteResolver
{
    protected MangoCallData $callData;

    public function __construct(MangoCallData $callData)
    {
        $this->callData = $callData;
    }

    public function resolve(?string $lineNumber, array $showroomIds = []): ?Site
    {
        $lineNumber = trim((string) $lineNumber);
        if ($lineNumber === '') {
            return null;
        }

        $sipAddress = Str::startsWith($lineNumber, 'sip:')
            ? Str::after($lineNumber, 'sip:')
            : $lineNumber;
        $sipUser = Str::contains($sipAddress, '@')
            ? Str::before($sipAddress, '@')
            : null;
        $phone = $this->callData->externalPhone($lineNumber);
        $phoneVariants = $phone
            ? [$phone, '+' . $phone, '8' . substr($phone, 1)]
            : [];

        $query = Site::query();
        $showroomIds = array_values(array_filter(array_unique($showroomIds)));
        if ($showroomIds) {
            $query->whereIn('showroom_id', $showroomIds);
        }

        return $query
            ->where(function ($query) use ($lineNumber, $sipAddress, $sipUser, $phoneVariants) {
                $query->where('phone', $lineNumber)
                    ->orWhere('second_phone', $lineNumber)
                    ->orWhere('sip', $lineNumber)
                    ->orWhere('sip', $sipAddress);

                if ($sipUser) {
                    $query->orWhere('sip', $sipUser)
                        ->orWhere('sip', 'sip:' . $sipUser)
                        ->orWhere('sip', 'LIKE', 'sip:' . $sipUser . '@%')
                        ->orWhere('sip', 'LIKE', $sipUser . '@%');
                }

                if ($phoneVariants) {
                    $query->orWhereIn('phone', $phoneVariants)
                        ->orWhereIn('second_phone', $phoneVariants);
                }
            })
            ->latest('updated_at')
            ->first();
    }
}
