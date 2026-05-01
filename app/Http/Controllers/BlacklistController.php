<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Log;

class BlacklistController extends Controller
{
    public function blacklists()
    {

        $key = 'blacklists'; // A unique cache key
        $blacklists = Cache::remember($key, now()->addMinutes(120), function () {
            // This closure will be executed if the cache key does not exist.
            return QueryBuilder::for(Blacklist::class)
                ->with(['showroom'])->get();
        });
        return response()->json($blacklists, 200);
    }


    public function save(Request $request)
    {


        $existing = Blacklist::where('phone', $request->item['phone'])->first();
        if ($existing) {
            return response()->json(['error' => 'Этот номер уже в черном списке'], 422);
        }
        $listing = new Blacklist;
        //$listing->id = $request->item['id'];
        $listing->phone = $request->item['phone'];

        $listing->showroom_id = $request->item['showroom_id'] ?? null;
        $listing->comment = $request->item['comment'] ?? null;
        $listing->save();
        Cache::forget('blacklists');
        Cache::forget('blacklist_' . $request->item['phone']);

        return response()->json($listing);
    }


    public function update(Request $request)
    {
        $listing = Blacklist::where('id', $request->item['id'])->first();

        $listing->phone = $request->item['phone'];

        $listing->showroom_id = $request->item['showroom_id'] ?? null;
        $listing->comment = $request->item['comment'] ?? null;
        $listing->save();
        Cache::forget('blacklists');
        Cache::forget('blacklist_' . $request->item['phone']);

        return response()->json($listing);
    }

    public function delete(Request $request)
    {
        $item = Blacklist::where('id', $request->id)->first();
        if (!$item) {
            return response()->json(['message' => 'Phone not found'], 404);
        }

        $item->delete();
        try {
            Cache::forget('blacklists');
            Cache::forget('blacklist_' . $request->item['phone']);
        } catch (\Exception $e) {
            Log::error($e);
        }

        return response()->json($item);
    }
}
