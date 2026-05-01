<?php

namespace App\Http\Controllers;

use App\Events\UsedCarAdded;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\UsedCar;
use App\Models\Transit;
use App\Models\UsedcarTransit;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Log;

class FiltersUsedCarShowroomId implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (!is_array($value)) {
            $query->where('showroom_id', $value)->orWhere('transit', '<>', '0');
        }
        else $query->whereIn('showroom_id', $value)->orWhere('transit', '<>', '0');
    }
}
class UsedCarController extends Controller
{
    public function cars(){
        $cars = QueryBuilder::for(UsedCar::class)
            ->allowedFilters([AllowedFilter::custom('showroom_id', new FiltersUsedCarShowroomId), AllowedFilter::scope('withTrashed')])->with(["model", "mark", "bodytype", "transmission", "enginetype", "wheeltype", "showroom", "status", "transits", "manager", "user"])->orderby("status_id")->get();
        return response()->json($cars, 201);
    }

    public function light_cars(){
        $cars = QueryBuilder::for(UsedCar::class)
            ->whereNotNull('auto_ru')->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::scope('withTrashed')])->with(["model", "mark", "bodytype", "transmission", "enginetype", "wheeltype"])->orderby("status_id")
            ->get(['id', 'auto_ru', 'year', 'owner_count', 'is_ready','milage', 'bodytype_id', 'enginetype_id','transmission_id', 'volume' , 'transit', 'status_id', 'deleted_at', 'mark_id', 'model_id', 'price_avito', 'salon', 'color', 'vin_number', 'wheeltype_id' ]);

        return response()->json($cars, 201);
    }

    public function getSold(){
        $cars = UsedCar::with(["model", "mark", "bodytype", "transmission", "enginetype", "wheeltype", "showroom", "status", "transits", "manager"])->withTrashed()->where("status_id",2)->get();
        return response()->json($cars, 201);
    }

    public function list(Request $request): JsonResponse
    {
        $milage_from = $request->milage_from ?? 0;
        $milage_to = $request->milage_to ?? 100000000;
        $year_from = $request->year_from ?? 1920;
        $year_to = $request->year_to ?? date('Y');
        $price_from = $request->price_from ?? 0;
        $price_to = $request->price_to ?? 100000000;
        $cars = QueryBuilder::for(UsedCar::class)
            ->allowedFilters(["model_id", "mark_id", "bodytype_id", "transmission_id", "enginetype_id", "wheeltype_id", "showroom_id", "status_id"])
            ->with(["model", "mark", "bodytype", "transmission", "enginetype", "wheeltype", "showroom", "status", "transits"])
            //->where("expose", '=', 1)
            ->whereBetween('year', array($year_from, $year_to))
            ->whereBetween('milage', [$milage_from, $milage_to])
            ->whereBetween('price', [$price_from, $price_to])

            ->paginate(16);
        return response()->json($cars, 200);
    }

    public function getUsedCar(Request $request){
        $usedcar = UsedCar::find($request->id);
        return response()->json($usedcar, 200);
    }

    public function save(Request $request){
        if(isset($request->item['id'])){
            $usedcar = UsedCar::withTrashed()->find($request->item['id']);
        }
        else $usedcar = new UsedCar;
        $usedcar->showroom_id = $request->item['showroom_id'];
        if (isset($request->item['income_date'])){
            $usedcar->income_date = date("Y-m-d", strtotime($request->item['income_date']));
        }else $usedcar->income_date = NULL;

        $usedcar->mark_id = $request->item['mark_id'];
        $usedcar->model_id = $request->item['model_id'];
        $usedcar->auto_ru = $request->item['auto_ru'];
        $usedcar->year = $request->item['year'];
        $usedcar->bodytype_id = $request->item['bodytype_id'];
        $usedcar->transmission_id = $request->item['transmission_id'];
        $usedcar->enginetype_id = $request->item['enginetype_id'];
        $usedcar->salon = $request->item['salon'];
        $usedcar->wheeltype_id = $request->item['wheeltype_id'];
        $usedcar->owner_count = $request->item['owner_count'];
        $usedcar->power = $request->item['power'];
        $usedcar->color = $request->item['color'];
        $usedcar->milage = $request->item['milage'] ?? 0;
        $usedcar->volume = $request->item['volume'];
        $usedcar->vin_number = $request->item['vin_number'];
        $usedcar->number = $request->item['number'];
        $usedcar->sts = $request->item['sts'];
        $usedcar->ptc = $request->item['ptc'];
        $usedcar->income_price = $request->item['income_price'];
        $usedcar->price = $request->item['price'];
        $usedcar->price_avito = $request->item['price_avito'] ?? null;
        $usedcar->key_number = $request->item['key_number'];
        $usedcar->manager_id = $request->item['manager_id'] ?? null;
        $usedcar->is_tradein = $request->item['is_tradein'] ?? null;
        $usedcar->status_id = $request->item['status_id'];
        $usedcar->registered = $request->item['registered'];
        $usedcar->transit = $request->item['transit'];
        $usedcar->comment = $request->item['comment'];
        $usedcar->repair_price = $request->item['repair_price'] ?? null;
        $usedcar->painting_price = $request->item['painting_price'] ?? null;
        $usedcar->preparation_price = $request->item['preparation_price'] ?? null;
        $usedcar->frontal_price = $request->item['frontal_price'] ?? null;
        $usedcar->milage_price = $request->item['milage_price'] ?? null;
        $usedcar->transport_price = $request->item['transport_price'] ?? null;
        $usedcar->removal_price = $request->item['removal_price'] ?? null;
        $usedcar->entity = $request->item['entity'] ?? null;

        $usedcar->repair = $request->item['repair'] ?? null;

        //$usedcar->repair_body = $request->item['repair_body'] ?? null;
        $usedcar->repair_painting = $request->item['repair_painting'] ?? null;
        $usedcar->repair_locksmith = $request->item['repair_locksmith'] ?? null;
        $usedcar->repair_electric = $request->item['repair_electric'] ?? null;
        $usedcar->repair_others = $request->item['repair_others'] ?? null;
        $usedcar->repair_dry = $request->item['repair_dry'] ?? null;
        $usedcar->repair_polishing = $request->item['repair_polishing'] ?? null;
        //$usedcar->milage_price = $request->item['milage_price'] ?? null;
        $usedcar->repair_windshield = $request->item['repair_windshield'] ?? null;

        $usedcar->body_status = $request->item['body_status'] ?? null;
        $usedcar->painting_status = $request->item['painting_status'] ?? null;
        $usedcar->locksmith_status = $request->item['locksmith_status'] ?? null;
        $usedcar->electric_status = $request->item['electric_status'] ?? null;
        $usedcar->others_status = $request->item['others_status'] ?? null;
        $usedcar->dry_cleaning_status = $request->item['dry_cleaning_status'] ?? null;
        $usedcar->polishing_status = $request->item['polishing_status'] ?? null;
        $usedcar->milage_status = $request->item['milage_status'] ?? null;
        $usedcar->windshield_status = $request->item['windshield_status'] ?? null;
        $usedcar->preparation_status = $request->item['preparation_status'] ?? null;

        $usedcar->body_comment = $request->item['body_comment'] ?? null;
        $usedcar->painting_comment = $request->item['painting_comment'] ?? null;
        $usedcar->locksmith_comment = $request->item['locksmith_comment'] ?? null;
        $usedcar->electric_comment = $request->item['electric_comment'] ?? null;
        $usedcar->others_comment = $request->item['others_comment'] ?? null;
        $usedcar->dry_cleaning_comment = $request->item['dry_cleaning_comment'] ?? null;
        $usedcar->polishing_comment = $request->item['polishing_comment'] ?? null;
        $usedcar->millage_comment = $request->item['millage_comment'] ?? null;
        $usedcar->windshield_comment = $request->item['windshield_comment'] ?? null;
        $usedcar->preparation_comment = $request->item['preparation_comment'] ?? null;







        //$usedcar->pictures = $request->item['pictures'] ?? [];
        //$usedcar->expose = $request->item['expose'] ?? 0;
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($usedcar, 201);
    }
    public function ready(Request $request){
        $usedcar = UsedCar::withTrashed()->find($request->id);
        $usedcar->is_ready = $request->isReady;
        $usedcar->diagnostic_before = $request->diagnostic_before;
        $usedcar->diagnostic_after = $request->diagnostic_after;
        $usedcar->dry_cleaning = $request->dry_cleaning;
        $usedcar->polish = $request->polish;
        $usedcar->painting = $request->painting;
        $usedcar->comment = $request->comment;
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($usedcar, 201);
    }
    public function savePurchase(Request $request){
        //return $request;
        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->comment_purchase = $request->item['comment_purchase'] ?? null;
        $usedcar->videos = $request->item['videos'];
        $usedcar->pictures = $request->item['pictures'];
        $usedcar->pictures2 = $request->item['pictures2'];
        $usedcar->repair_price = $request->item['repair_price'] ?? 0;
        $usedcar->painting_price = $request->item['painting_price'] ?? 0;
        $usedcar->preparation_price = $request->item['preparation_price'] ?? 0;
        $usedcar->frontal_price = $request->item['frontal_price'] ?? 0;
        $usedcar->milage_price = $request->item['milage_price'] ?? 0;
        $usedcar->transport_price = $request->item['transport_price'] ?? 0;
        $usedcar->removal_price = $request->item['removal_price'] ?? 0;
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($usedcar, 201);
    }

    public function saveStorage(Request $request){

        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->showroom_id = $request->item['showroom_id'];
        $usedcar->mark_id = $request->item['mark_id'];
        $usedcar->model_id = $request->item['model_id'];
        $usedcar->year = $request->item['year'];
        $usedcar->bodytype_id = $request->item['bodytype_id'];
        $usedcar->transmission_id = $request->item['transmission_id'];
        $usedcar->enginetype_id = $request->item['enginetype_id'];
        $usedcar->wheeltype_id = $request->item['wheeltype_id'];
        $usedcar->color = $request->item['color'];
        //$usedcar->milage = $request->item['milage'] ?? 0;
        $usedcar->volume = $request->item['volume'];
        $usedcar->price = $request->item['price'];
        $usedcar->status_id = $request->item['status_id'];
        $usedcar->pictures = $request->item['pictures'] ?? [];
        $usedcar->expose = $request->item['expose'] ?? 0;
        $usedcar->comment = $request->item['comment'];
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($usedcar, 201);
    }
    public function delete(Request $request){
        $usedcar = UsedCar::withTrashed()->find($request->id);
        if (isset($usedcar) && $usedcar->deleted_at !== null){

            $usedcar->forceDelete();
        } else {
            $usedcar->user_id = Auth::id();
            $usedcar->save();
            $usedcar->delete();
        }
        UsedCarAdded::dispatch($usedcar);
        return response()->json($usedcar, 200);
    }
    public function transit(Request $request){

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
    public function cancelTransit(Request $request){
        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->transit = 0;
        $usedcar->save();
        $transfer = UsedcarTransit::where('usedcar_id',$request->item['id'])->where('status', 0)->latest()->first();;
        $transfer->delete();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($transfer, 201);
    }
    public function approveTransit(Request $request){
        $transfer = UsedcarTransit::where('usedcar_id',$request->item['id'])->where('status', 0)->latest()->first();
        $transfer->receiver_id = $request->item['receiver_id'];
        $transfer->status = 1;

        $usedcar = UsedCar::find($request->item['id']);
        $usedcar->transit = 0;
        $usedcar->showroom_id = $transfer->showroom_id;
        $transfer->save();
        $usedcar->save();
        UsedCarAdded::dispatch($usedcar);
        return response()->json($transfer, 201);
    }

    public function transits(){
        $transits = UsedcarTransit::with(["usedcar", "usedcar.mark", "usedcar.model", "usedcar.bodytype", "usedcar.wheeltype", "usedcar.enginetype", "usedcar.transmission"  ,"showroom", "sender", "sender.role", "receiver", "receiver.role"])->where("status", 1)->orderBy('created_at', 'desc')->get();
        return response()->json($transits, 201);
    }
    public function statuses(){
        $statuses = Status::get();
        return response()->json($statuses, 201);
    }


    public function upload(Request $request){
        $fileName = $request->file->getClientOriginalName();
        $request->file->move(public_path('images/usedcar/'), $fileName);
        return response()->json(['file' => $fileName]);
    }

    public function deletePhoto($url) {
        $image_path = public_path("images/usedcar/{$url}");
        if (File::exists($image_path)) {
            //File::delete($image_path);
            unlink($image_path);
            return response()->json("$url успешно удалён");
        }
        else  return response()->json("Произошла ошибка при удаление $url", 500);

    }



    public function histories(Request $request)
    {
        $id = $request->id;
        //return $id;
        $items = ActivityLog::with(['user'])->where('subject_type', "App\Models\UsedCar")->where('subject_id', $id)->orderBy('created_at', 'DESC')->get();
        return response()->json($items, 200);
    }

}
