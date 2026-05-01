<?php

namespace App\Http\Controllers;

use App\Events\ArrivalCreated;
use App\Events\ConsultationProceed;
use App\Events\CreditCreated;
use App\Models\Arrival;
use App\Models\CreditRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class ArrivalController extends Controller
{
    public $today;

    public function __construct()
    {
        $this->today = Carbon::today()->format('Y-m-d');
    }

    public function arrivals(Request $request)
    {

        $items  = QueryBuilder::for(Arrival::class)
            ->allowedFilters([AllowedFilter::scope('between'), AllowedFilter::scope('search'), AllowedFilter::exact('showroom_id'), AllowedFilter::exact('status_id'), AllowedFilter::exact('site_id')])->with(['region', 'manager', 'operator', 'showroom', 'site'])->orderBy('is_avito', 'ASC')->orderBy('is_cash', 'ASC')->orderBy('date', 'ASC')->orderBy('not_coming', 'ASC')->orderBy('visited', 'ASC')->orderBy('not_responding', 'ASC')->get();


        return response()->json($items, 201);
    }

    public function arrival_consultations($from = null, $to = null): JsonResponse
    {
        $filter_from = $from ?? $this->today;
        $filter_to = $to ?? $this->today;
        $data = Arrival::with(['manager', 'operator', 'showroom', 'site'])->orderBy('date', 'ASC')->orderBy('not_coming', 'ASC')->orderBy('visited', 'ASC')->orderBy('not_responding', 'ASC')->orderBy('consultation_comment', 'ASC')->where('is_consultation', 1)->whereBetween('date', [$filter_from, $filter_to])->get();
        return response()->json($data, 201);
    }

    public function all_arrivals()
    {

        $data = Arrival::where('date', $this->today)->get();
        return response()->json($data, 201);
    }

    public function saveArrival(Request $request)
    {
        //return response()->json($request, 201);
        if (isset($request->item['id'])) {
            $arrival = Arrival::find($request->item['id']);
        } else $arrival = new Arrival;
        if ($request->item['transit'] === true && $request->item['transit_confirmed'] !== true) {
            $this->sendNotify("Приезд", $request->item['showroom_id'], $request->item['client_name']);
            $arrival->transit = $request->item['transit'];

        } else if ($request->item['transit'] === 1 && $request->item['transit_confirmed'] === true) {
            $this->sendNotifyOperator("Приезд", $request->item['showroom_id'], $request->item['client_name']);
        }
        $arrival->date = $request->item['date'] ?? now()->format('Y-m-d');
        $arrival->client_name = $request->item['client_name'];
        $arrival->showroom_id = $request->item['showroom_id'];
        $arrival->site_id = $request->item['site_id'] ?? null;
        $arrival->region_id = $request->item['region_id'];
        $arrival->car_name = $request->item['car_name'];
        $arrival->phone_number = $request->item['phone_number'];
        $arrival->price = $request->item['price'];
        $arrival->payment = $request->item['payment'];
        $arrival->payment_method = $request->item['payment_method'] ?? null;
        $arrival->initial_fee = $request->item['initial_fee'];
        $arrival->operator_id = $request->item['operator_id'];
        $arrival->comments = $request->item['comments'];
        $arrival->sd_comment = $request->item['sd_comment'];
        $arrival->manager_id = $request->item['manager_id'];
        $arrival->visited = $request->item['visited'];
        $arrival->not_coming = $request->item['not_coming'];
        $arrival->not_responding = $request->item['not_responding'];
        $arrival->consultation = $request->item['consultation'];
        $arrival->is_consultation = $request->item['is_consultation'] ?? null;
        $arrival->is_cash = $request->item['is_cash'] ?? null;
        if ($request->item['is_avito'] === false) {
            $arrival->is_avito = null;
        } else {
            $arrival->is_avito = $request->item['is_avito'] ?? null;
        }
        if ($request->item['is_cash'] === false) {
            $arrival->is_cash = null;
        } else {
            $arrival->is_cash = $request->item['is_cash'] ?? null;
        }
        $arrival->jump = $request->item['jump'];
        $arrival->transit = $request->item['transit'];
        $arrival->transit_confirmed = $request->item['transit_confirmed'];
        $arrival->transit_consultation = $request->item['transit_consultation'];
        $arrival->save();
        ArrivalCreated::dispatch($arrival);
        return response()->json($arrival, 201);
    }

    public function saveConsultation(Request $request)
    {
        //return $request->id;
        if (isset($request->id)) {
            $arrival = Arrival::find($request->id);

            if ($request->hasFile('audio_file')) {
                /*
                $music_file = $request->file('audio_file');

                // This will return "mp3" not the file name
                $file_ext = $music_file->getClientOriginalExtension();
                $file_name = '23256565'.'.'.$file_ext;

                // This will return /audio/mp3
                $location = public_path('audio/');

                // This will move the file to /public/audio/mp3/
                // and save it as "mp3" (not what you want)
                // example: /public/audio/mp3/mp3 (without extension)
                $music_file->move($location,$file_name);
                //Storage::copy($location, $file_name);
                //$request->file->move(public_path('audio'), $file_name);
                $arrival->audio = $file_name; */


                $file = $request->file('audio_file');
                // File extension
                $extention = $file->extension();
                // File name ex: my-audio-song.mp3
                $name = Str::uuid() . '.' . $extention;
                // Path
                $public_path = public_path('audio');
                // Save location /public/audio/mp3
                $location = $public_path;
                // Move file to /public/audio/mp3 and save it as my-audio-song.mp3
                $file->move($location, $name);
                // Save link to the file /audio/mp3/my-audio-song.mp3
                // So in your view you can link to it.
                $arrival->audio = $name;

            }
            $arrival->consultation_comment = $request->consultation_comment ?? null;
            $arrival->save();
            ConsultationProceed::dispatch($arrival);
            return response()->json($arrival, 201);

        } else return response()->json([], 201);
    }

    public function deleteArrival(Request $request)
    {
        $arrival = Arrival::find($request->id);
        if (isset($arrival)) {
            ArrivalCreated::dispatch($arrival);
            $arrival->delete();
        }
        return response()->json($arrival, 200);
    }

    public function deleteTrashArrivals(Request $request)
    {
        $arrival = Arrival::withTrashed()->find($request->id);
        $arrival->forceDelete();
        return response()->json($arrival, 200);
    }


    public function copyArrival(Request $request)
    {
        //return response()->json($request, 201);
        if (isset($request->id)) {
            $arrival = Arrival::find($request->id);
            if (!empty($arrival)) {
                $arrival->visited = 1;
                try {
                    $arrival->forwarded = 1;
                } catch (Throwable $e) {
                    Log::emergency('$arrival->forwarded');
                }
                $arrival->save();
                $credit_request = new CreditRequest();
                $credit_request->date = date('Y-m-d');
                $credit_request->client_name = $arrival->client_name;
                $credit_request->phone_number = $arrival->phone_number;
                $credit_request->manager_id = $arrival->manager_id;
                $credit_request->region_id = $arrival->region_id;
                $credit_request->live_region = $arrival->region_id;
                $credit_request->showroom_id = $arrival->showroom_id;
                $credit_request->site_id = $arrival->site_id;
                $credit_request->save();
                CreditCreated::dispatch($credit_request);
                return response()->json($credit_request, 201);
            }
        } else {
            return response()->json(["message" => "Arrival not found"], 201);
        }


    }
}
