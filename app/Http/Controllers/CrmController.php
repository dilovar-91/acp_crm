<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmController extends Controller
{
    public function setWorkPlace(Request $request){
        $user = Auth::user();
        $user->work_place = $request->work_place;
        $user->save();
        return $user;

    }
}
