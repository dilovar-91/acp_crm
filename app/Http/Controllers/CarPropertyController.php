<?php

namespace App\Http\Controllers;

use App\Models\BodyType;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\EngineType;
use App\Models\Status;
use App\Models\Transmission;
use App\Models\WheelType;
use App\Models\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CarPropertyController extends Controller
{

    public function carmodels($mark_id){
        $models = Cache::remember('car_models_' . $mark_id, now()->addMinutes(320), function () use ($mark_id) {
            return CarModel::where('brand_id', $mark_id)->orderBy('name')->get();
        });

        return response()->json($models, 200);
    }
    public function allModels(){

        $key = 'all_models';
        $models = Cache::remember($key, now()->addMinutes(320), function () {
            return CarModel::select('id', 'name', 'brand_id')->orderby('name')->get();
        });
        return response()->json($models, 200);
    }
    public function carMarks(){

        $key = 'marks';
        $marks = Cache::remember($key, now()->addMinutes(320), function () {
            return Brand::orderby('name')->get();
        });
        return response()->json($marks, 200);
    }
    public function bodytypes(){
        $key = 'bodytypes';
        $bodytypes = Cache::remember($key, now()->addMinutes(320), function () {
            return BodyType::get();
        });
        return response()->json($bodytypes, 200);
    }
    public function enginetypes(){
        $key = 'enginetypes';
        $enginetypes = Cache::remember($key, now()->addMinutes(320), function () {
            return EngineType::get();
        });
        return response()->json($enginetypes, 200);
    }
    public function transmissions(){
        $key = 'transmissions';
        $transmissions = Cache::remember($key, now()->addMinutes(320), function () {
            return Transmission::get();
        });
        return response()->json($transmissions);
    }
    public function colors(){
        $key = 'colors';
        $colors = Cache::remember($key, now()->addMinutes(320), function () {
            return Color::get();
        });
        return response()->json($colors, 201);
    }
    public function wheeltypes(){

        $key = 'wheeltypes';
        $wheeltypes = Cache::remember($key, now()->addMinutes(320), function () {
            return WheelType::get();
        });
        return response()->json($wheeltypes, 201);
    }
    public function statuses(): JsonResponse
    {
        $key = 'car_statuses';
        $statuses = Cache::remember($key, now()->addMinutes(320), function () {
            return Status::get();
        });
        return response()->json($statuses, 201);
    }
}
