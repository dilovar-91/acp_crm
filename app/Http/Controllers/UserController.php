<?php

namespace App\Http\Controllers;

use App\Models\AuthLog;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function users(Request $request): JsonResponse
    {
        $users = User::with(['showroom', 'role'])->get();
        return response()->json($users);
    }
    public function usersByShowroom(Request $request)
    {

        $users =QueryBuilder::for(User::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])->with('role')->get();
        return response()->json($users);
    }
    public function sessions(Request $request): JsonResponse
    {
        $users = QueryBuilder::for(AuthLog::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])->with(['user'])->paginate(15);
        return response()->json($users);
    }

    public function getUser($id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'active' => 'nullable|boolean',
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'password' => 'required|min:6',
            'role_id' => 'required|integer|exists:roles,id',
            'showroom_id' => 'required|integer',
            'work_place' => 'nullable|string|max:255',
        ]);

        $id = $request->id;
        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'User info updated successfully'], 200);
    }

    public function save(Request $request)
    {
        $request->validate([
            'active' => 'nullable|boolean',
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'password' => 'required|min:8',
            'role_id' => 'required|integer|exists:roles,id',
            'showroom_id' => 'required|integer',
            'work_place' => 'nullable|string|max:255',
        ]);
        $user = new User();
        $user->first_name  = $request->first_name ?? null;
        $user->last_name  = $request->last_name ?? null;
        $user->email  = $request->email ?? null;
        $user->role_id  = $request->role_id ?? null;
        $user->active  = $request->active ?? null;
        if($request->password !== null){
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return response()->json(['message' => 'User info updated successfully'], 200);
    }


    public function setOnline(Request $request){
        $user = Auth::user();
        $user->online = !$user->online;
        $user->save();
        return $user;
    }


    public function roles()
    {
        return Cache::remember('user_roles', now()->addMinutes(600), function () {
            return Role::get();
        });
    }
}
