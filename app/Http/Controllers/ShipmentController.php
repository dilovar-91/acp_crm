<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\ShipmentCar;
use App\Models\ShipmentDetail;
use App\Models\ShipmentStatus;
use App\Models\ShipmentUsedCar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ShipmentController extends Controller
{
    public function shipments(): JsonResponse
    {
        $shipments= QueryBuilder::for(Shipment::class)
            ->allowedFilters([AllowedFilter::scope('mark'), AllowedFilter::scope('showroom'), AllowedFilter::scope('created_between')])
            ->with(["from_showroom", "showroom", "sender", "receiver", "sender.role", "receiver.role", 'status', 'cars', 'cars.car', 'cars.car.mark', 'cars.car.model', 'used_cars', 'used_cars.car', 'used_cars.car.mark', 'used_cars.car.model'])->orderBy('created_at', 'desc')->get();
        return response()->json($shipments, 201);
    }
    public function companies(): JsonResponse
    {
        $items= Company::orderBy('name')->get();
        return response()->json($items, 201);
    }
    public function detail($id): JsonResponse
    {
        $item= ShipmentDetail::find($id);
        return response()->json($item, 201);
    }
    public function drivers(): JsonResponse
    {
        $items= Driver::orderBy('name')->get();
        return response()->json($items, 201);
    }
    public function statuses(): JsonResponse
    {
        $items= ShipmentStatus::get();
        return response()->json($items, 201);
    }

    public function save(Request $request){

        if (isset($request->item['id']) ){
            $item = Shipment::where('id', $request->item['id'])->first();
        }
        else $item = new Shipment;
        $item->showroom_id = $request->item['showroom_id'];
        $item->name = $request->item['name'];
        $item->sender_id = $request->item['sender_id'];
        $item->receiver_id = $request->item['receiver_id'];
        $item->from = $request->item['from'];
        $item->driver = $request->item['driver'];
        $item->estimated_date = $request->item['estimated_date'];
        $item->shipment_price = $request->item['shipment_price'];
        $item->company_id = $request->item['company_id'];
        $item->status_id = $request->item['status_id'];
        $item->status_id = $request->item['status_id'] ?? null;
        $item->comment = $request->item['comment'];
        $item->save();
        //ShipmentAdded::dispatch($item);
        return response()->json($item, 201);
    }
    public function addCar(Request $request){
        $item = new ShipmentCar;
        $item->car_id = $request->item['car_id'];
        $item->shipment_id = $request->item['shipment_id'];
        $item->quantity = 1;
        $item->save();
        return response()->json($item, 201);
    }
    public function addUsedCar(Request $request){
        $item = new ShipmentUsedCar;
        $item->car_id = $request->item['car_id'];
        $item->shipment_id = $request->item['shipment_id'];
        $item->quantity = 1;
        $item->save();
        return response()->json($item, 201);
    }

    public function delete($id): JsonResponse
    {
        $item = Shipment::where('id', $id)->first();
        $item->delete();
        return response()->json($item);
    }
}
