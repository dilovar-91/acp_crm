<?php

namespace App\Http\Controllers;

use App\Events\CarAdded;
use App\Events\UsedCarAdded;
use App\Models\Car;
use App\Models\Transit;
use App\Models\UsedCar;
use App\Models\UsedcarTransit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransitController extends Controller
{
    public function transits(): JsonResponse
    {
        $transits = QueryBuilder::for(Transit::class)
            ->allowedFilters([AllowedFilter::scope('mark'), AllowedFilter::scope('showroom'), AllowedFilter::scope('created_between')])->with(["car", "from_showroom", "showroom", "sender", "receiver", "sender.role", "receiver.role", "car.mark",  "car.model", "car.transmission", "car.wheeltype",])->where('status', 1)->orderBy('created_at', 'desc')->get();
        return response()->json($transits, 201);
    }

    public function usedcar_transits(): JsonResponse
    {

        $items = QueryBuilder::for(UsedcarTransit::class)
            ->allowedFilters([AllowedFilter::scope('mark'), AllowedFilter::scope('showroom'), AllowedFilter::scope('created_between')])->with(["usedcar", "showroom",  "from_showroom", "sender", "receiver", "sender.role", "receiver.role", "usedcar.mark",  "usedcar.model", "usedcar.transmission", "usedcar.wheeltype",])->where('status', 1)->orderBy('created_at', 'desc')->get();
        return response()->json($items, 201);
    }

    public function transit(Request $request){
        $car = Car::find($request->item['id']);
        $car->transit = 1;
        $car->save();
        $transfer = new Transit();
        $transfer->car_id = $request->item['id'];
        $transfer->sender_id = $request->item['sender_id'];
        $transfer->showroom_id = $request->item['showroom_id'];
        $transfer->from = $request->item['from'];
        $transfer->comment = $request->item['transit_comment'];
        $transfer->save();
        CarAdded::dispatch($car);
        return response()->json($transfer, 201);
    }
    public function cancelTransit(Request $request){
        $car = Car::find($request->item['id']);
        $car->transit = 0;
        $car->save();
        $transfer = Transit::where('car_id',$request->item['id'])->where('status', 0)->latest()->first();
        $transfer->delete();
        CarAdded::dispatch($car);
        return response()->json($transfer, 201);
    }
    public function approveTransit(Request $request){

        $transfer = Transit::where('car_id',$request->item['id'])->where('status', 0)->latest()->first();
        $transfer->receiver_id = $request->item['receiver_id'];
        $transfer->from = $request->item['showroom_id'];
        $transfer->status = 1;
        $car = Car::find($request->item['id']);
        $car->showroom_id = $transfer->showroom_id;
        $car->transit = 0;
        $car->save();
        $transfer->save();
        CarAdded::dispatch($car);
        return response()->json($car, 201);
    }


    public function usedTransit(Request $request){

        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->transit = 1;
        $usedcar->save();
        $transfer = new UsedcarTransit();
        $transfer->usedcar_id = $request->item['id'];
        $transfer->sender_id = $request->item['sender_id'];
        $transfer->showroom_id = $request->item['showroom_id'];
        $transfer->from = $request->item['from'];
        $transfer->comment = $request->item['comment'];
        $transfer->save();
        UsedCarAdded::dispatch($usedcar);

        return response()->json($usedcar, 201);
    }
    public function cancelUsedTransit(Request $request)
    {
        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->transit = 0;
        $usedcar->save();
        $transfer = UsedcarTransit::where('usedcar_id',$request->item['id'])->where('status', 0)->latest()->first();;
        $transfer->delete();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($transfer, 201);
    }
    public function approveUsedTransit(Request $request){
        $transfer = UsedcarTransit::where('usedcar_id',$request->item['id'])->where('status', 0)->latest()->first();
        $transfer->receiver_id = $request->item['receiver_id'];
        $transfer->from = $request->item['showroom_id'];
        $transfer->status = 1;

        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->transit = 0;
        $usedcar->showroom_id = $transfer->showroom_id;
        $transfer->save();
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($transfer, 201);
    }
}
