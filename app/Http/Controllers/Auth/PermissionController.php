<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bouncer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions from current logged user.
     *
     * @return JsonResponse
     */
    public function abilities()
    {
        return auth()->user()->getAbilities()->pluck('name');
        return response()->json(['data'=>$data]);
    }


    public function retract()
    {
        $user = auth()->user();
        //return Bouncer::retract('director')->from($user);
        return Bouncer::retract('director')->from($user);
    }
    public function disallow()
    {
        $user = auth()->user();
        return  response()->json(Bouncer::disallow($user)->to('view'));
        //return response()->json(Bouncer::disallow($user)->to('view22'));
        //return response()->json( Bouncer::disallow($user)->to('edit', Car::class));
        //return response()->json(Bouncer::disallow($user)->to('delete'));
        //return response()->json(Bouncer::forbid($user)->to('view22'));
        //return $user->disallow('view');
    }
    public function forbid()
    {
        $user = auth()->user();
        return response()->json(Bouncer::forbid($user)->to('view'));
        //return $user->disallow('view');
    }


    public function create()
    {
        $edit = Bouncer::ability()->firstOrCreate([
            'name' => 'edit_field_price',
            'title' => 'Изменить цену транзита',
        ]);
        return response()->json($edit);
    }


    public function allow()
    {
        $user = auth()->user();
        return response()->json(Bouncer::allow($user)->to('see_cars/1'));
    }


    public function sync(Request $request)
    {
        //$user_from = $request->user_from;
        //$user = User::find($user_from);
        //$abilities = $user->getAbilities();
        //$user_to = $request->user_to;
        //$user2= User::find($user_to);
       // $roles = $user->getRoles();
       // $user2->syncRoles($roles);

        $user_from = $request->user_from;
        $user = User::find($user_from);
        // Get the abilities of the source user
        $abilities = $user->getAbilities();
        // Get the roles of the source user
        $roles = $user->getRoles();
        // Get the target user (to whom to sync abilities and roles)
        $user_to = $request->user_to;
        $user2 = User::find($user_to);

        Bouncer::sync($user2)->abilities($abilities);
        Bouncer::sync($user2)->roles($roles);
        Cache::flush();
        return response()->json([
            'message' => 'Доступы и роли успешно синхронизированы.',
            'user' => $user2
        ]);
    }


    public function users(Request $request)
    {

        return;//response()->json(Bouncer::sync($user2)->abilities($abilities));
    }

    public function user_permissions($id)
    {
        $user = User::where('id', $id)->first();
        $items = $user->getAbilities()->pluck('name');
        return response()->json($items);
    }

    public function allAbilities(): JsonResponse
    {
        $abilities = Bouncer::ability()
            ->select(['id', 'name', 'title'])
            ->orderBy('name')
            ->get();

        return response()->json($abilities);
    }

    public function userPermissionRows($id): JsonResponse
    {
        $rows = DB::table('permissions')
            ->where('entity_type', User::class)
            ->where('entity_id', (int)$id)
            ->where('forbidden', 0)
            ->pluck('ability_id');

        return response()->json($rows);
    }

    public function saveUserPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'ability_ids' => 'array',
            'ability_ids.*' => 'integer|exists:abilities,id',
        ]);

        $currentUser = $request->user();
        if (!$currentUser || (int)$currentUser->role_id !== 1) {
            return response()->json(['message' => 'Недостаточно прав'], 403);
        }

        $userId = (int)$request->input('user_id');
        $abilityIds = collect($request->input('ability_ids', []))
            ->map(fn ($id) => (int)$id)
            ->unique()
            ->values();

        DB::transaction(function () use ($userId, $abilityIds) {
            DB::table('permissions')
                ->where('entity_type', User::class)
                ->where('entity_id', $userId)
                ->delete();

            if ($abilityIds->isNotEmpty()) {
                $rows = $abilityIds->map(function ($abilityId) use ($userId) {
                    return [
                        'ability_id' => $abilityId,
                        'entity_id' => $userId,
                        'entity_type' => User::class,
                        'forbidden' => 0,
                        'scope' => null,
                    ];
                })->all();

                DB::table('permissions')->insert($rows);
            }
        });

        Cache::flush();

        return response()->json([
            'message' => 'Доступы пользователя сохранены',
            'user_id' => $userId,
        ]);
    }
}
