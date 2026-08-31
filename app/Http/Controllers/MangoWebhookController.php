<?php

namespace App\Http\Controllers;

use App\Services\Mango\MangoCallService;
use Illuminate\Support\Facades\Log;
use Sharoff\Mango\Api\MangoHelper;
use Throwable;

class MangoWebhookController extends Controller
{
    protected MangoCallService $calls;

    public function __construct(MangoCallService $calls)
    {
        $this->calls = $calls;
    }

    public function call($id)
    {
        return $this->process($id, 'call', function (object $payload, int $accountId) {
            $this->calls->handleRealtime($payload, $accountId);
        });
    }

    public function summary($id)
    {
        return $this->process($id, 'summary', function (object $payload, int $accountId) {
            $this->calls->handleSummary($payload, $accountId);
        });
    }

    public function recordAdded($id)
    {
        return $this->process($id, 'record_added', function (object $payload, int $accountId) {
            $this->calls->handleRecording($payload, $accountId);
        });
    }

    protected function process($id, string $event, callable $handler)
    {
        $accountId = (int) $id;

        try {
            MangoHelper::setApiKey(config('mango.api_key_' . $accountId))
                ->setApiSalt(config('mango.api_salt_' . $accountId));
            $payload = MangoHelper::getMethodData();
            $this->log('info', $event, [
                'account_id' => $accountId,
                'entry_id' => $payload->entry_id ?? null,
                'payload' => $payload,
            ]);

            $handler($payload, $accountId);

            return response()->json(['status' => 'ok']);
        } catch (Throwable $e) {
            $this->log('error', $event . ' failed', [
                'account_id' => $accountId,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        try {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/mango.log'),
                'level' => 'debug',
            ])->{$level}($message, $context);
        } catch (Throwable $e) {
            Log::{$level}('Mango: ' . $message, $context);
        }
    }
}
