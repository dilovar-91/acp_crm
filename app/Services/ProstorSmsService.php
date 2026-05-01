<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProstorSmsService
{
    public function send(
        string $phone,
        string $text,
        int $showroom_id,
        ?string $sender = null,
        ?string $scheduleTimeUtc = null,   // например: 2008-07-12T14:30:01Z
        ?string $statusQueueName = null
    ): array {
        $baseUrl = rtrim(config('services.prostor_sms.base_url'), '/');
        $login   = (string) config('services.prostor_sms.login_'.$showroom_id);
        $pass    = (string) config('services.prostor_sms.password_'.$showroom_id);

        if ($login === '' || $pass === '') {
            throw new \RuntimeException('Prostor SMS credentials are not set.');
        }

        $query = [
            'phone' => $phone,
            'text'  => $text, // Laravel/Http сам корректно закодирует query, UTF-8 ок
        ];

        if ($sender !== null) {
            $query['sender'] = $sender;
        }
        if ($scheduleTimeUtc !== null) {
            $query['scheduleTime'] = $scheduleTimeUtc;
        }
        if ($statusQueueName !== null) {
            $query['statusQueueName'] = $statusQueueName;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->withBasicAuth($login, $pass)
            ->get("{$baseUrl}/messages/v2/send/", $query);

        // если API возвращает не-2xx — бросаем исключение с телом ответа
        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new \RuntimeException(
                'Prostor SMS request failed: '.$response->status().' '.$response->body(),
                $response->status(),
                $e
            );
        }

        // В зависимости от API может быть JSON или текст.
        $contentType = $response->header('Content-Type', '');
        if (str_contains($contentType, 'application/json')) {
            return $response->json() ?? [];
        }

        return [
            'raw' => $response->body(),
            'status' => $response->status(),
        ];
    }
}
