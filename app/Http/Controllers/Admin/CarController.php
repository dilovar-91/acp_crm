<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CarController extends Controller
{
    public function marks(): JsonResponse
    {
        $marks =  QueryBuilder::for(Brand::class)->orderBy('id')->get();
        return response()->json($marks, 200);
    }
    public function models(){
        $models =  QueryBuilder::for(CarModel::class)
            ->allowedFilters([AllowedFilter::exact('brand_id')])->with('brand')->orderBy('name')->get();
        return response()->json($models, 200);
    }

    public function saveMark(Request $request)
    {
        $listing = new Brand;
        $listing->name = $request->item['name'];
        $listing->save();
        Cache::forget('marks');
        return response()->json($listing, 201);
    }

    public function updateMark(Request $request)
    {
        $item = Brand::find($request->item['id']);
        $item->name = $request->item['name'];
        $item->save();
        Cache::forget('marks');
        return response()->json($item, 201);
    }

    public function deleteMark(Request $request)
    {
        $mark = Brand::find($request->id);
        if (!$mark) {
            return response()->json(['message' => 'Mark not found'], 404);
        }
        $mark->delete();
        Cache::forget('marks');
        return response()->json($mark);
    }

    public function saveModel(Request $request)
    {
        $item = new CarModel();
        $item->name = $request->item['name'];
        $item->brand_id = $request->item['mark_id'];
        $item->save();
        Cache::forget('models');
        return response()->json($item, 201);
    }

    public function updateModel(Request $request)
    {
        $item = CarModel::find($request->item['id']);
        $item->name = $request->item['name'];
        $item->brand_id = $request->item['mark_id'];
        $item->save();
        Cache::forget('models');
        return response()->json($item, 201);
    }

    public function deleteModel(Request $request)
    {
        $item = CarModel::find($request->id);
        if (!$item) {
            return response()->json(['message' => 'Model not found'], 404);
        }
        $item->delete();
        Cache::forget('models');
        return response()->json($item);
    }
}
