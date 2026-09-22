<?php

namespace App\Services\Mango;

use App\Helpers\GeneralHelper;

class MangoCallData
{
    public const UNKNOWN = null;
    public const INTERNAL = 0;
    public const INCOMING = 1;
    public const OUTGOING = 2;

    public function directionFromRealtime(object $payload): ?int
    {
        $fromExtension = $payload->from->extension ?? null;
        $toExtension = $payload->to->extension ?? null;
        $fromPhone = $this->externalPhone($payload->from->number ?? null);
        $toPhone = $this->externalPhone($payload->to->number ?? null);
        $lineNumber = $payload->to->line_number ?? null;

        // Callback/click-to-call: первый leg звонит оператору и выглядит как
        // входящий (клиент в from, оператор в to). По доке Mango — исходящий.
        if ($this->isCallback($payload)) {
            return self::OUTGOING;
        }

        if ($fromExtension !== null && $toExtension !== null && !$fromPhone && !$toPhone) {
            return self::INTERNAL;
        }

        if ($fromExtension !== null && $toPhone) {
            return self::OUTGOING;
        }

        // Оба конца — внешние номера (исходящий с линии без extension в событии).
        if ($fromPhone && $toPhone) {
            return self::OUTGOING;
        }

        // Входящий: внешний номер в from. Connected иногда без line_number —
        // достаточно to.extension (кто принимает) или line_number.
        if (
            $fromPhone
            && $fromExtension === null
            && ($lineNumber !== null || ($toExtension !== null && $toExtension !== ''))
        ) {
            return self::INCOMING;
        }

        return self::UNKNOWN;
    }

    /**
     * Исходящий callback / кампания ИО.
     * Первый leg звонит оператору и выглядит как входящий (клиент в from).
     * @see MangoOffice VPBX API §3.1.2 callback_initiator, task_id
     */
    public function isCallback(object $payload): bool
    {
        if (trim((string) ($payload->callback_initiator ?? '')) !== '') {
            return true;
        }

        // ObDial / CallbackWidget / MissGroupCallCallback
        return isset($payload->task_id) && $payload->task_id !== '' && $payload->task_id !== null;
    }

    public function clientPhone(object $payload, int $direction): ?string
    {
        if ($direction === self::INCOMING) {
            return $this->externalPhone($payload->from->number ?? null);
        }

        if ($direction === self::OUTGOING) {
            return $this->externalPhone($payload->to->number ?? null);
        }

        return null;
    }

    public function operatorExtension(object $payload, int $direction): ?string
    {
        $extension = $direction === self::INCOMING
            ? ($payload->to->extension ?? null)
            : ($payload->from->extension ?? null);

        return $extension !== null && $extension !== ''
            ? (string) $extension
            : null;
    }

    public function lineNumber(object $payload, bool $summary = false): ?string
    {
        $value = $summary
            ? ($payload->line_number ?? null)
            : ($payload->to->line_number ?? null);

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    public function externalPhone($number): ?string
    {
        $number = trim((string) $number);
        if (
            $number === ''
            || stripos($number, 'sip:') !== false
            || strpos($number, '@') !== false
            || stripos($number, 'mangosip.ru') !== false
        ) {
            return null;
        }

        return GeneralHelper::normalizePlus7Phone($number);
    }
}
