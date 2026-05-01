<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\AuthLog;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuthLogController extends Controller
{
    public function logs(Request $request)
    {
        $logs =  QueryBuilder::for(AuthLog::class)
            ->allowedFilters([AllowedFilter::exact('authenticatable_id'), AllowedFilter::scope('between'), AllowedFilter::scope('ByShowroomId')])->with(['user', 'user.showroom'])->orderBy('id', 'desc')->paginate(15);
        return  response()->json($logs);
    }
}
