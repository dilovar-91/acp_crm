<?php

namespace App\Services;

use App\Models\Blacklist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use Throwable;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

class BlacklistService
{
    public function checkPhoneInBlacklistOrSpam($phone, $order)
    {

        $blacklist = Cache::remember('blacklist_' . $phone, now()->addMinutes(800), function () use ($phone) {
            return Blacklist::search($phone)->first();
        });
        //$pattern = '/^(\+?796517[01]|(\+796723[23][0-9]{4})|(\+?7963609[0-9]{4})|(\+?7963610[0-9]{4}))|(796517[01]|796723[23][0-9]{4}|7963609[0-9]{4}|7963610[0-9]{4}|7965390[0-9]{4}|7965416[0-9]{4}|7965415[0-9]{4}|7927846[0-9]{4}|7906775[0-9]{4}|7909631[0-9]{4}|7967057[0-9]{4}|7967060[0-9]{4})/';
        //$pattern = '/^\+?7(96517[01]|96723[23]|963609|963610|965390|965416|965415|927846|906775|909631|967057|967060|967112|968927|968925|930099|968926|92785|92765)\d{4}$/';
        $pattern = '/^\+?7((96517[01]|96723[23]|963609|963610|965390|965416|965415|927846|906775|909631|967057|967060|967112|968927|968925|930099|968926)\d{4}|(92785|96865)\d{5})$/';
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $parsedPhone = $phoneUtil->parse($phone, 'RU');
            $phone = $phoneUtil->format($parsedPhone, PhoneNumberFormat::E164);
        } catch (NumberParseException | Throwable $e) {
            $phone = preg_replace('/[^0-9+]/', '', $phone) ?? null;
        }
        if ($blacklist) {
            $this->updateOrderWithBlacklist($order, "ЧС");
        } else if (preg_match($pattern, $phone)) {
            $this->updateOrderWithBlacklist($order, "Спам");
        }
    }

    protected function updateOrderWithBlacklist($order, $comment)
    {
        $order->form_id = $order->showroom_id;
        $order->showroom_id = 16;
        $order->comment = $comment;
        $order->save();
    }
}
