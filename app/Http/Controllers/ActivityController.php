<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityController extends Controller
{


    public function car_activities(Request $request)
    {
        $users = QueryBuilder::for(ActivityLog::class)
            ->allowedFilters(['causer_id', 'email', 'subject_id', AllowedFilter::scope('created_at'), AllowedFilter::scope('mark'), AllowedFilter::scope('model')])->where('subject_type', 'App\\Models\\Car')->with(['user'])->get();
        return response()->json($users);
    }
    public function used_activities(Request $request)
    {
        $users = QueryBuilder::for(ActivityLog::class)
            ->allowedFilters(['causer_id', 'email', 'subject_id', AllowedFilter::scope('created_at'), AllowedFilter::scope('mark'), AllowedFilter::scope('showroom'), AllowedFilter::scope('model')])->where('subject_type', 'App\\Models\\UsedCar')->with(['user'])->get();
        return response()->json($users);
    }
}

