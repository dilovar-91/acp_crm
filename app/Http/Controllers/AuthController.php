<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        //Log::emergency("In_array");
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        /*
        $user = Auth::user();
        if (in_array($user->role_id, [2])) {
            if ($this->inSchedule($user)) {

            }
        } */

        $request->session()->regenerate();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(null, 401);
        }

        $impersonatorId = $request->session()->get('impersonator_id');
        $impersonator = null;
        if ($impersonatorId) {
            $impersonator = User::query()
                ->select(['id', 'first_name', 'last_name', 'role_id'])
                ->find($impersonatorId);
        }

        return response()->json(array_merge($user->toArray(), [
            'is_impersonating' => (bool)$impersonator,
            'impersonator' => $impersonator,
        ]));
    }

    public function impersonate(Request $request, $id)
    {
        $id = (int)$id;
        $currentUser = $request->user();
        if (!$currentUser || (int)$currentUser->role_id !== 1) {
            return response()->json(['message' => 'Недостаточно прав'], 403);
        }

        if ((int)$currentUser->id === (int)$id) {
            return response()->json(['message' => 'Вы уже вошли под этим пользователем'], 422);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $request->session()->put('impersonator_id', $currentUser->id);
        Auth::guard('web')->login($targetUser);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Успешный вход от имени пользователя',
            'user_id' => $targetUser->id,
        ]);
    }

    public function stopImpersonation(Request $request)
    {
        $impersonatorId = $request->session()->get('impersonator_id');
        if (!$impersonatorId) {
            return response()->json(['message' => 'Режим входа от имени пользователя не активен'], 422);
        }

        $impersonator = User::find($impersonatorId);
        if (!$impersonator) {
            $request->session()->forget('impersonator_id');
            return response()->json(['message' => 'Администратор не найден'], 404);
        }

        Auth::guard('web')->login($impersonator);
        $request->session()->forget('impersonator_id');
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Вы вернулись в аккаунт администратора',
            'user_id' => $impersonator->id,
        ]);
    }

    public function inSchedule($user)
    {
        $from = null;
        $to = null;

        $ot = Carbon::now();
        $do = Carbon::now();

        $ot->hour = 19;
        $ot->minute = 00;

        $ot->second = 00;
        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $from = Carbon::now();
            $to = Carbon::now()->addDays(1);
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
            $from = Carbon::now()->subDays(1);
            $to = Carbon::now();
        }


        $from->hour = 19;
        $from->minute = 00;
        $from->second = 00;
        $to->hour = 19;
        $to->minute = 00;
        $to->second = 00;


        $res = User::with(['schedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])
            ->whereIn('role_id', [2, 6])
            ->where('showroom_id', $user->id)
            ->whereHas('schedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->first();
    }


    public function clearCache()
    {
        Cache::flush();
        return response()->json(['message' => 'Cache cleared successfully']);
    }

}
