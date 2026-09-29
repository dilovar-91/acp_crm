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
        $fromExtension = $this->extensionValue($payload->from->extension ?? null);
        $toExtension = $this->extensionValue($payload->to->extension ?? null);
        $fromPhone = $this->externalPhone($payload->from->number ?? null);
        $toPhone = $this->externalPhone($payload->to->number ?? null);
        $lineNumber = $payload->to->line_number ?? null;

        // Callback / ИО / API-команда исходящего (§3.1.2 callback_initiator, task_id, command_id).
        if ($this->isOutboundApiCall($payload)) {
            return self::OUTGOING;
        }

        // Сотрудник ВАТС в from = исходящий или внутренний, никогда входящий.
        // Пример softphone: from.extension + to.number
        // Пример callback: from.extension + to.extension (+ номера линий)
        if ($fromExtension !== null) {
            if ($toExtension !== null && !$fromPhone && !$toPhone) {
                return self::INTERNAL;
            }

            return self::OUTGOING;
        }

        // Оба конца — внешние номера (исходящий с линии без extension в событии).
        if ($fromPhone && $toPhone) {
            return self::OUTGOING;
        }

        // Входящий: внешний номер в from, сотрудник/линия в to.
        // Connected иногда без line_number — достаточно to.extension.
        if (
            $fromPhone
            && ($lineNumber !== null || $toExtension !== null)
        ) {
            return self::INCOMING;
        }

        return self::UNKNOWN;
    }

    /**
     * Исходящий, инициированный API/кампанией/виджетом.
     * Первый leg callback звонит оператору и выглядит как входящий.
     *
     * @see MangoOffice VPBX API §3.1.2 callback_initiator, task_id, command_id
     * @see «Инициирование исходящего вызова»: command_id есть, taken_from_call_id нет
     * @see «Маршрутизация»: у нового leg на сотрудника есть taken_from_call_id — это входящий
     */
    public function isOutboundApiCall(object $payload): bool
    {
        if (trim((string) ($payload->callback_initiator ?? '')) !== '') {
            return true;
        }

        if (isset($payload->task_id) && $payload->task_id !== '' && $payload->task_id !== null) {
            return true;
        }

        $commandId = trim((string) ($payload->command_id ?? ''));
        if ($commandId === '') {
            return false;
        }

        // route/transfer на сотрудника всегда несёт taken_from_call_id предыдущего плеча.
        $takenFrom = trim((string) ($payload->from->taken_from_call_id ?? ''));

        return $takenFrom === '';
    }

    /** @deprecated use isOutboundApiCall */
    public function isCallback(object $payload): bool
    {
        return $this->isOutboundApiCall($payload);
    }

    public function clientPhone(object $payload, int $direction): ?string
    {
        if ($direction === self::INCOMING) {
            return $this->externalPhone($payload->from->number ?? null);
        }

        if ($direction === self::OUTGOING) {
            return $this->externalPhone($payload->to->number ?? null)
                ?: $this->externalPhone($payload->from->number ?? null);
        }

        return null;
    }

    public function operatorExtension(object $payload, int $direction): ?string
    {
        $extension = $direction === self::INCOMING
            ? ($payload->to->extension ?? null)
            : ($payload->from->extension ?? null);

        return $this->extensionValue($extension);
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

    protected function extensionValue($extension): ?string
    {
        if ($extension === null || $extension === '') {
            return null;
        }

        return (string) $extension;
    }
}
