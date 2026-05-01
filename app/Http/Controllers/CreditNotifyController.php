<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\CreditApprove;
use App\Notifications\NotifyApproveManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class CreditNotifyController extends Controller
{
    public function sendNotify(Request  $request){
        $item = $request->item;
        $usersToForwardTo = [277174745];
        $client_name = $item['client_name'];
        foreach ($usersToForwardTo as $userId) {
            try {
                Telegram::bot('pilight')->setAsyncRequest(false)
                    ->sendMessage(['chat_id' => $userId, 'text' => "Клиент по имени *$client_name* одобрен по кредиту. (*".$item['showroom']['name']."*)", 'parse_mode'=>'Markdown']);
            } catch (Throwable $e) {
                Log::warning("bot error", [$e]);
            }
        }
    }

    public function notifyManager(Request  $request){
        //$users = User::where('role_id', 1)->get();
        //$item = $request->item;
        //$res = Notification::send($users, new CreditApprove($item));
        //$users->notify(new CreditApprove($item, $receivers));
        return  response()->json($request, 201);

    }
}
