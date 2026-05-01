<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataAvController extends Controller
{
    public function report(Request $request){

        $number =  $request->number;

        if ($number == trim($number) && strpos($number, ' ') !== false) {
            return;
        }
        $url = "https://data.av100.ru/reportrequest.ashx";
        $request_params = array(
            "key" => "207ef5b8-294d-400e-9cc7-84090887246d",
            "mode" => "setqueue",
            "gosnumber" => $number
        );

        $get_params = http_build_query($request_params);

// Запрос к серверу
        $response = file_get_contents($url."?".$get_params);
// Преобразование ответа
        $result = json_decode($response);
        return $result;

    }
    public function number(Request $request){

        $number =  $request->number;

        if ($number == trim($number) && strpos($number, ' ') !== false) {
            return;
        }

        $url = "https://data.av100.ru/api.ashx";
        $request_params = array(
            "key" => "207ef5b8-294d-400e-9cc7-84090887246d",
            "gosnomer" => $number
        );

        $get_params = http_build_query($request_params);

// Запрос к серверу
        $response = file_get_contents($url."?".$get_params);
// Преобразование ответа
        $result = json_decode($response);
        //Log::emergency($result);
        return $result;

    }
}
