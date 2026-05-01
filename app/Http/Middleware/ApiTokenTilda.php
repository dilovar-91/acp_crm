<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class ApiTokenTilda
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->api_key != env('API_KEY_TILDA')) {
            try {
                $chatId = '277174745';
                Telegram::bot('pilight')->setAsyncRequest(false)
                    ->sendMessage(['chat_id' => $chatId, 'text' => "Неправильный токен Tilda $request->api_token телефон: $request->phone точка входа: $request->entry_point (accas.ru)"]);
            } catch (Throwable $e) {
                Log::error("bot error", [$e]);
            }
            Log::error("Unauthorized api_token_tilda", [$request]);
            return response()->json('Unauthorized', 401);
        }
        return $next($request);
    }
}
