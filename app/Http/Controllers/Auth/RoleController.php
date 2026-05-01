<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    /**
     * Display a listing of roles from current logged user.
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        $user = auth()->user();
        return Cache::remember('user_roles_' . $user->id, now()->addMinutes(600), function () use ($user) {
            return $user->getRoles();
        });
    }


    public function operator_roles()
    {
       return Cache::remember('operator_roles', now()->addMinutes(600), function () {
            return Role::whereIn('id', [2,3,6])->get();
        });
    }
}
