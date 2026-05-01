<?php

namespace App\Http\Controllers;

use App\Models\SmsMessage;
use App\Services\ProstorSmsService;
use Illuminate\Http\Request;

class ProstorSmsController extends Controller
{
    public function sendSms(Request $request, ProstorSmsService $sms)
    {
        $data = $request->validate([
            'showroom_id' => ['required', 'integer', 'min:1'],
            'phone' => ['required', 'string'],
            'text'  => ['required', 'string'],
            'sender' => ['nullable', 'string', 'max:32'],
            'scheduleTime' => ['nullable', 'date'], // если прилетает ISO8601 - ок; иначе лучше отдельная обработка
            'statusQueueName' => ['nullable', 'string', 'min:3', 'max:16', 'regex:/^[a-zA-Z0-9]+$/'],
        ]);

        $rawPhone = $data['phone'];

// убираем всё, кроме цифр
        $digits = preg_replace('/\D+/', '', $rawPhone);

// нормализация под РФ
        if (str_starts_with($digits, '8')) {
            $digits = '7' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '7')) {
            throw new \InvalidArgumentException('Некорректный номер телефона');
        }

        $phone = '+' . $digits;

        // создаём запись заранее (queued)
        $smsRow = SmsMessage::create([
            'showroom_id' => $data['showroom_id'],
            'phone' => $phone,
            'text' => $data['text'],
            'sender' => $data['sender'] ?? null,
            'schedule_time_utc' => $data['scheduleTime'] ?? null,
            'status_queue_name' => $data['statusQueueName'] ?? null,
            'status' => 'queued',
        ]);

        try {
            $result = $sms->send(
                phone: $data['phone'],
                showroom_id: $data['showroom_id'],
                text: $data['text'],
                sender: $data['sender'] ?? null,
                scheduleTimeUtc: isset($data['scheduleTime'])
                    ? \Carbon\Carbon::parse($data['scheduleTime'])->utc()->toIso8601ZuluString()
                    : null,
                statusQueueName: $data['statusQueueName'] ?? null
            );

            // тут зависит от формата ответа API: подстрой поля под реальные ключи
            $smsRow->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_message_id' => $result['messageId'] ?? $result['id'] ?? null,
                'provider_response' => $result,
            ]);

            return response()->json([
                'id' => $smsRow->id,
                'status' => $smsRow->status,
                'provider' => $result,
            ]);
        } catch (\Throwable $e) {
            $smsRow->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'id' => $smsRow->id,
                'status' => $smsRow->status,
                'error' => $e->getMessage(),
            ], 502);
        }
    }
}
