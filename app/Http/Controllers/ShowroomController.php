<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bank;
use App\Models\Operator;
use App\Models\Region;
use App\Models\Showroom;
use App\Models\Site;
use App\Models\SiteType;
use App\Models\User;
use App\Models\WorkPlace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ShowroomController extends Controller
{
    public function managers(): JsonResponse
    {

        $key = 'managers';
        $managers = Cache::remember($key, now()->addMinutes(3600), function () {
            return QueryBuilder::for(User::class)
                ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::scope('second')])->where('role_id', 7)->orderBy('first_name')->get();
        });
        return response()->json($managers, 200);
    }

    public function second_managers(): JsonResponse
    {
        $key = 'second_managers';
        $managers = Cache::remember($key, now()->addMinutes(3600), function () {
            return QueryBuilder::for(User::class)
                ->allowedFilters([AllowedFilter::exact('showroom_id')])->where('role_id', 8)->orderBy('first_name')->get();
        });
        return response()->json($managers, 200);
    }

    public function preparers(): JsonResponse
    {
        $key = 'preparers';
        $items = Cache::remember($key, now()->addMinutes(3600), function () {
            return QueryBuilder::for(User::class)
                ->allowedFilters([AllowedFilter::exact('showroom_id')])->where('role_id', 9)->orderBy('first_name')->get();
        });
        return response()->json($items, 200);
    }

    public function operators(): JsonResponse
    {
        $operators = QueryBuilder::for(User::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::scope('OrderWorkers')])->with('showroom')->whereIn('role_id', [2, 3, 6])->orderBy('first_name')->get();

        return response()->json($operators, 200);
    }

    public function showrooms(): JsonResponse
    {
        $key = 'showrooms';
        $showrooms = Cache::remember($key, now()->addMinutes(3600), function () {
            return Showroom::orderBy('sort')->get();
        });
        return response()->json($showrooms, 200);
    }

    public function showroom($id): JsonResponse
    {
        $result = Showroom::where("id", $id)->first();
        return response()->json($result, 200);
    }

    public function sites(): JsonResponse
    {
        $sites = QueryBuilder::for(Site::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])->with('showroom')->orderBy('order')->get();
        return response()->json($sites, 200);
    }

    public function site_types()
    {
        $key = 'site_types';
        $items = Cache::remember($key, now()->addMinutes(3600), function () {
            return SiteType::get();
        });
        return response()->json($items, 200);
    }

    public function admin_sites(): JsonResponse
    {
        $key = 'admin_sites';
        $sites = Cache::remember($key, now()->addMinutes(3600), function () {
            return QueryBuilder::for(Site::class)
                ->allowedFilters([AllowedFilter::exact('showroom_id')])->with(['showroom', 'agency'])->orderBy('id', 'DESC')->get();
        });
        return response()->json($sites, 200);
    }

    public function saveSite(Request $request)
    {
        $listing = new Site;
        $listing->title = $request->item['title'];
        $listing->phone = $request->item['phone'];
        $listing->url = $request->item['url'] ?? null;
        $listing->pic = $request->item['pic'] ?? null;
        $listing->description = $request->item['description'] ?? null;
        $listing->showroom_id = $request->item['showroom_id'];
        $listing->type_id = $request->item['type_id'] ?? null;
        $listing->agency_id = $request->item['agency_id'] ?? null;
        $listing->active = $request->item['active'] ?? 1;
        $listing->actual = $request->item['actual'] ?? null;
        $listing->order = $request->item['order'] ?? null;
        $listing->token = $request->item['token'] ?? null;
        $listing->post_url = $request->item['post_url'] ?? null;
        $listing->calltouch_access_key = $request->item['calltouch_access_key'] ?? null;
        $listing->calltouch_site_id = $request->item['calltouch_site_id'] ?? null;
        $listing->mango_site_id = $request->item['mango_site_id'] ?? null;
        $listing->mango_token = $request->item['mango_token'] ?? null;
        $listing->autospot_id = $request->item['autospot_id'] ?? null;
        $listing->sip = $request->item['sip'] ?? null;
        $listing->save();
        Cache::forget('sites');
        Cache::forget('admin_sites');

        Cache::forget('sites_agency_32');
        return $listing;
    }

    public function updateSite(Request $request)
    {
        $listing = Site::where('id', $request->item['id'])->first();
        $listing->title = $request->item['title'];
        $listing->phone = $request->item['phone'];
        $listing->url = $request->item['url'] ?? null;
        $listing->pic = $request->item['pic'] ?? null;
        $listing->description = $request->item['description'] ?? null;
        $listing->showroom_id = $request->item['showroom_id'];
        if (isset($request->item['to_showroom'])) {
            $listing->to_showroom = $request->item['to_showroom'] ?? null;
        }
        $listing->type_id = $request->item['type_id'] ?? null;
        $listing->agency_id = $request->item['agency_id'] ?? null;
        $listing->active = $request->item['active'] ?? null;
        $listing->actual = $request->item['actual'] ?? null;
        $listing->order = $request->item['order'] ?? null;
        $listing->token = $request->item['token'] ?? null;
        $listing->post_url = $request->item['post_url'] ?? null;

        $listing->mango_site_id = $request->item['mango_site_id'] ?? null;
        $listing->mango_token = $request->item['mango_token'] ?? null;

        $listing->calltouch_access_key = $request->item['calltouch_access_key'] ?? null;
        $listing->calltouch_site_id = $request->item['calltouch_site_id'] ?? null;
        $listing->autospot_id = $request->item['autospot_id'] ?? null;
        $listing->sip = $request->item['sip'] ?? null;

        $listing->save();
        Cache::forget('sites');
        Cache::forget('admin_sites');

        Cache::forget('sites_agency_32');
        return $listing;
    }

    public function duplicateSite(Request $request)
    {
        $item = Site::where('id', $request->id)->first();
        $duplicatedItem = $item->replicate();
        $duplicatedItem->mango_site_id = null;
        $duplicatedItem->mango_token = null;
        $duplicatedItem->token = null;
        $duplicatedItem->calltouch_access_key = null;
        $duplicatedItem->calltouch_site_id = null;
        $duplicatedItem->sip = null;

        $duplicatedItem->save(); // Save the duplicated item to the database
        Cache::forget('sites');
        Cache::forget('admin_sites');

        Cache::forget('sites_agency_32');
        return $duplicatedItem;
    }

    public function deleteSite(Request $request)
    {
        $site = Site::find($request->id);

        if (!$site) {
            return response()->json(['message' => 'Site not found'], 404);
        }

        $site->delete();
        Cache::forget('sites');
        Cache::forget('admin_sites');
        Cache::forget('sites_agency_32');

        return response()->json($site);
    }

    public function banks(): JsonResponse
    {

        $key = 'banks';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return Bank::orderBy('order')->orderBy('name')->where('active', 1)->get();
        });
        return response()->json($items, 200);
    }

    public function regions()
    {
        $key = 'regions';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return Region::orderBy('name')->get();
        });
        return response()->json($items, 200);
    }

    public function agencies()
    {

        $key = 'agencies';
        $items = Cache::remember($key, now()->addMinutes(320), function () {
            return Agency::orderBy('name')->get();
        });
        return response()->json($items, 200);
    }

    public function saveOperator(Request $request)
    {
        if (isset($request->item['id'])) {
            $item = User::where('id', $request->item['id'])->firstOrFail();
        };
        $item->first_name = $request->item['first_name'];
        $item->last_name = $request->item['last_name'] ?? null;
        $item->email = $request->item['email'];
        $item->save();
        return response()->json($item, 201);
    }
    public function resetPasswords(Request $request)
    {
        $request->validate([
            'showroom_id' => 'required|integer',
            'main_password' => 'required|string',
            'operator_password' => 'required|string',
        ]);

        $showroom_id = $request->showroom_id;
        $mainPass = $request->main_password;
        $operatorPass = $request->operator_password;

        DB::beginTransaction();

        try {

            // Обновляем главных (role_id 3 и 6)
            User::where('showroom_id', $showroom_id)
                ->whereIn('role_id', [3, 6])
                ->update([
                    'password' => bcrypt($mainPass),
                ]);

            // Обновляем операторов (role_id 2)
            User::where('showroom_id', $showroom_id)
                ->where('role_id', 2)
                ->update([
                    'password' => bcrypt($operatorPass),
                ]);

            DB::commit();


            $response = Http::withToken(env('PLANSHETKA_API_KEY'))
                ->post('https://acp77.ru/api/reset-operator-passwords', [
                    'showroom_id' => $showroom_id,
                    'main_password' => $mainPass,
                    'operator_password' => $operatorPass,
                ]);





            if ($response->successful()) {

                return response()->json([
                    'message' => 'Успешно обновлено',
                    'crm_message' => $response->json('message'),
                    'admins_logins' => $response->json('admins_logins'),
                    'operators_logins' => $response->json('operators_logins'),
                ], 200);

            } else {

                return response()->json([
                    'message' => 'Ошибка API',
                    'status' => $response->status(),
                    'response' => $response->body(),
                ], $response->status());
            }

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Ошибка при обновлении',
                'error' => $e->getMessage()
            ], 500);
        }

    }



    public function work_places(): JsonResponse
    {
        $operators = QueryBuilder::for(WorkPlace::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id')])
            ->orderBy('number')
            ->pluck('number');

        if ($operators->isEmpty()) {
            return response()->json([]);
        }
        return response()->json($operators);
    }
}
