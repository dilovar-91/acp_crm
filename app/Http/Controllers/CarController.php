<?php

namespace App\Http\Controllers;


use App\Events\CarAdded;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CarController extends Controller
{
    public function cars(){
        $cars= QueryBuilder::for(Car::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::exact('mark_id'), AllowedFilter::exact('is_sold'), 'model_id','bodytype_id','transmission_id','enginetype_id','is_sold', AllowedFilter::scope('withTrashed')])->with(["model", "mark", "bodytype", "transmission", "enginetype", "wheeltype", "showroom", "transits"])->get();
        return response()->json($cars, 201);
    }

    public function save(Request $request){

        if (isset($request->item['id']) ){
            $car = Car::withTrashed()->where('id', $request->item['id'])->first();
        }
        else $car = new Car;
        $car->showroom_id = $request->item['showroom_id'];
        $car->link = $request->item['link'] ?? null;
        $car->mark_id = $request->item['mark_id'];
        $car->model_id = $request->item['model_id'];
        $car->year = $request->item['year'];
        $car->transmission_id = $request->item['transmission_id'];
        $car->complectation = $request->item['complectation'] ?? NULL;
        $car->enginetype_id = $request->item['enginetype_id'];
        $car->wheeltype_id = $request->item['wheeltype_id'];
        $car->power = $request->item['power'];
        $car->color = $request->item['color'];
        $car->volume = $request->item['volume'];
        $car->vin_number = $request->item['vin_number'];
        $car->body_number = $request->item['body_number'];
        $car->ptc_type = $request->item['ptc_type'];
        $car->price = $request->item['price'];
        $car->avito_price = $request->item['avito_price'] ?? null;
        $car->supplier = $request->item['supplier'] ?? null;
        $car->key_number = $request->item['key_number'];
        $car->is_sold = $request->item['is_sold'] ?? null;
        $car->status_id = $request->item['status_id'] ?? null;
        $car->comment = $request->item['comment'];
        $car->save();
        CarAdded::dispatch($car);
        return response()->json($car, 201);
    }


    public function delete(Request $request){
        $car = Car::withTrashed()->find($request->id);
        if (isset($car) && $car->deleted_at !== null){
            $car->forceDelete();
        } else $car->delete();
        CarAdded::dispatch($car);
        return response()->json($car, 200);
    }


    public function test(Request $request){
        $car = CarModel::with(['brand'])->paginate(15);
        return response()->json($car, 200);
    }


}
