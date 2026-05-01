<?php

namespace App\Http\Controllers;

use App\Models\OperatorSchedule;
use App\Models\ProgrammaticSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function schedules(Request $request): JsonResponse
    {
        $showroom_id = $request->showroom_id;

        $items= OperatorSchedule::whereHas('operator', function ($query) use ($showroom_id) {
            return $query->where('showroom_id', '=', $showroom_id)->whereIn('role_id', [2, 6]);
        })->with('operator')->get();
        return response()->json($items, 201);
    }



    public function programmaticSchedules(Request $request): JsonResponse
    {
        $showroom_id = $request->showroom_id;
        $items= ProgrammaticSchedule::whereHas('operator', function ($query) use ($showroom_id) {
            return $query->where('showroom_id', '=', $showroom_id);
        })->with('operator')->get();
        return response()->json($items, 201);
    }

    public function updateSchedule(Request $request): JsonResponse
    {
        if (isset($request->item['id']) ){
            $item = OperatorSchedule::where('id', $request->item['id'])->first();
            if (empty($item)) return response()->json($item, 201);
            $item->monday = $request->item['monday'] ?? null;
            $item->tuesday = $request->item['tuesday'] ?? null;
            $item->wednesday = $request->item['wednesday'] ?? null;
            $item->thursday = $request->item['thursday'] ?? null;
            $item->friday = $request->item['friday'] ?? null;
            $item->saturday = $request->item['saturday'] ?? null;
            $item->sunday = $request->item['sunday'] ?? null;
            $item->user_id = $request->item['user_id'];
            $item->save();
        }

        return response()->json($item, 201);
    }

    public function updateProgrammaticSchedule(Request $request): JsonResponse
    {
        if (isset($request->item['id']) ){
            $item = ProgrammaticSchedule::where('id', $request->item['id'])->first();
            if (empty($item)) return response()->json($item, 201);
            $item->monday = $request->item['monday'] ?? null;
            $item->tuesday = $request->item['tuesday'] ?? null;
            $item->wednesday = $request->item['wednesday'] ?? null;
            $item->thursday = $request->item['thursday'] ?? null;
            $item->friday = $request->item['friday'] ?? null;
            $item->saturday = $request->item['saturday'] ?? null;
            $item->sunday = $request->item['sunday'] ?? null;
            $item->user_id = $request->item['user_id'];
            $item->save();
        }

        return response()->json($item, 201);
    }

    public function createSchedule(Request $request): JsonResponse
    {

        $item = new OperatorSchedule();
        $item->monday = $request->item['monday'] ?? null;
        $item->tuesday = $request->item['tuesday'] ?? null;
        $item->wednesday = $request->item['wednesday'] ?? null;
        $item->thursday = $request->item['thursday'] ?? null;
        $item->friday = $request->item['friday'] ?? null;
        $item->saturday = $request->item['saturday'] ?? null;
        $item->sunday = $request->item['sunday'] ?? null;
        $item->user_id = $request->item['user_id'];
        $item->save();
        return response()->json($item, 201);
    }

    public function createProgrammaticSchedule(Request $request): JsonResponse
    {

        $item = new ProgrammaticSchedule();
        $item->monday = $request->item['monday'] ?? null;
        $item->tuesday = $request->item['tuesday'] ?? null;
        $item->wednesday = $request->item['wednesday'] ?? null;
        $item->thursday = $request->item['thursday'] ?? null;
        $item->friday = $request->item['friday'] ?? null;
        $item->saturday = $request->item['saturday'] ?? null;
        $item->sunday = $request->item['sunday'] ?? null;
        $item->user_id = $request->item['user_id'];
        $item->save();
        return response()->json($item, 201);
    }


    public function deleteSchedule(Request $request): JsonResponse
    {
        $item = OperatorSchedule::find($request->id);
        if (isset($item)){
            $item->delete();
        }
        return response()->json($item, 200);
    }


    public function deleteProgrammaticSchedule(Request $request): JsonResponse
    {
        $item = ProgrammaticSchedule::find($request->id);
        if (isset($item)){
            $item->delete();
        }
        return response()->json($item, 200);
    }
}
