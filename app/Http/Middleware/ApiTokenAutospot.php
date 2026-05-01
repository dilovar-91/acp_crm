<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class ApiTokenAutospot
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken(); // получаем Bearer токен

        if ($token !== env('API_KEY_AUTOSPOT')) {
            try {
                $chatId = '277174745';

                Telegram::bot('pilight')
                    ->setAsyncRequest(false)
                    ->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Неправильный Bearer токен Autospot: {$token}, телефон: {$request->phone}, точка входа: {$request->entry_point} (accas.ru)"
                    ]);

            } catch (Throwable $e) {
                Log::error("bot error", [$e]);
            }

            Log::error("Unauthorized api_token_autospot", [
                'token' => $token,
                'ip' => $request->ip()
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
