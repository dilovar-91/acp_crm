<?php

namespace App\Http\Controllers;

use App\Events\MangoIncome;
use App\Events\OrderCreated;
use App\Events\ClearNotify;
use App\Helpers\GeneralHelper;
use App\Models\Order;
use App\Models\OrderHistory;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Sharoff\Mango\Api\MangoHelper;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\PhoneCode;
use App\Models\Test;



class GravitelController extends Controller
{
    public function events(Request $request, $id){


        /*$req = array(
            'callid' => '76ce4abd-8cb0-439f-9f6d-e06d47fab309',
            'cmd' => 'event',
            'crm_token' => 'er4gt34y45h',
            'diversion' => '74952481316',
            'ext' => '701',
            'groupRealName' => 'Отдел продаж',
            'phone' => '79197241539',
            'type' => 'INCOMING',
            'user' => 'admin@vats19910.gravitel.ru',
            'z-flag' => '0',
        );
        $request = $req;*/
        $phone = $request['phone'];
        Log::emergency($request);
        if ($request['type'] == "INCOMING" && $request['cmd'] == 'event') {
            $def = substr($phone, 1, 3);
            $last = substr($phone, -7, 7);
            $info = PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
            $order = Order::search($phone)->with(['operator', 'region'])->orderBy('retries', 'ASC')->orderBy('id', 'ASC')->latest()->first();
            $another_showroom = null;
            Log::emergency($order);
            if ($order->showroom_id !== null && $order->showroom_id !== $id){
                $another_showroom = true;
            }
            $data = array(
                "phone" => $phone,
                "phone_region" => $info->region ?? null,
                "region" => $order->region ?? null,
                "line_number" => $request['diversion'] ?? null,
                "work_place" => $request['ext'] ?? null,
                "client_name" => ($order->last_name ?? null) . ' ' . ($order->first_name ?? null),
                "operator" => $order->operator ?? null,
                "date" => $order->created_at ?? null,
                "another_showroom" => $another_showroom
            );



            MangoIncome::dispatch($data, $id);
        } else if($request['cmd'] == 'event' && $request['type'] == "CANCELLED") {
            ClearNotify::dispatch($request->all(), $id);
        }

        if ($request['cmd'] == 'history') {
            $order = Order::search($phone)->with(['operator', 'region'])->orderBy('retries', 'ASC')->orderBy('id', 'ASC')->latest()->first();
            Log::emergency($request);
            $history = new OrderHistory();
            $history->phone = $request->phone;
            $history->datetime = $request->start;
            $history->ext = $request->ext;
            $history->diversion = $request->diversion;
            $history->link = $request->link;
            $history->telnum = $request->telnum;
            $history->type = $request->type;
            $history->save();
        }


    }


    public function call(Request $request)
    {
        $id = $request->showroom_id;
        $client = new Client();
        Log::emergency([
            'cmd' => 'makeCall',
            'phone' => $request->phone,
            'user' => 'sotrudnik1@lightauto.gravitel.ru',
            'token' => config('gravitel.key_'.$id),
        ]);
        $response = $client->post(config('gravitel.domain_'.$id),   [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'cmd' => 'makeCall',
                'phone' => $request->phone,
                'user' => 'sotrudnik1@lightauto.gravitel.ru',
                'token' => config('gravitel.key_'.$id),
            ])
        ]);
        //  Log::emergency($response);
        return response()->json($response);
    }



    public function sms(Request $request): JsonResponse
    {
        //return response()->json($request);
        MangoHelper::setApiKey(config('mango.api_key'))->setApiSalt(config('mango.api_salt'));
        $response = MangoHelper::sendSms($request->ext_number, $request->phone, $request->text);
        return response()->json($response);
    }

    public function getContent(Request $request): JsonResponse
    {
        $url = 'https://app.mango-office.ru/vpbx/commands/callback';

        $data = array(
            "command_id" => "ID" . rand(10000000, 99999999),
            "from" => array(
                "extension" => "111", // внутренний номер, за счет которого производится звонок. (например 101)

                // "number" => "74951200480" // <- кто звонит (можно SIP)
                //"number" => "" // <- (можно номер)

            ),
            "to_number" => $request->phone // <- кому звонит
        );
        $json = json_encode($data);

        $sign = hash('sha256', config('mango.api_key') . $json . config('mango.api_salt'));
        $postdata = array(
            'vpbx_api_key' => config('mango.api_key'),
            'sign' => $sign,
            'json' => $json
        );
        $post = http_build_query($postdata);
        /************ Отправка с использованием file_get_contents ************/
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        return response()->json($response);
    }


    public function decoded(Request $request)
    {

        $point = "https://light-auto.com/?utm_source=yandex&utm_medium=cpc&utm_campaign=!ПОИСК+/+NISSAN+ALMERA+/+Дальние+&utm_term=купить+нисс";

        $url = 'https://lpcrm.ru/graphql';

        $data = '{"query":"mutation addOrder{\n                                      addOrder(type_id: 2, last_name: \"Проверка\", phone: \"+799999999999999\", mark_id: 1, model_id: 1, entry_point: \"' . $point . '\", utm_campaign: ' . ($request->input('utm_campaign') ? '\"' . $request->input('utm_campaign') . '\"' : 'null') . ', utm_term: ' . ($request->input('utm_term') ? '\"' . ($request->input('utm_term') ?? '') . '\"' : 'null') . ', utm_source: ' . ($request->input('utm_source') ? '\"' . $request->input('utm_source') . '\"' : 'null') . ', utm_medium: ' . ($request->input('utm_medium') ? '\"' . $request->input('utm_medium') . '\"' : 'null') . ', comment: \"Форма\", ip_address: \"' . $request->ip() . '\") {\n                                            id\n                                            last_name\n                                            comment\n                                            ip_address\n                                        }\n                                    }"}';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($status != 201) {
            //die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }

        curl_close($curl);
        $response = json_decode($json_response, true);
        return mb_convert_encoding(urldecode($request->input('utm_term')));

    }

    public function clearNotify(Request $request)
    {
        ClearNotify::dispatch($request->all());
    }

}
