<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function getTabs(){
        $items = Cache::remember('tab_pages', now()->addMinutes(120), function () {
            return Page::with(['children'])
                ->where('is_tab', 1)
                ->orderBy('order')
                ->get();
        });

        return response()->json($items);
    }

    public function pages(){

        $items = Cache::remember('all_pages', now()->addMinutes(120), function () {
            return Page::with(['children'])->orderBy('order')->get();
        });
        return response()->json($items);
    }

}
