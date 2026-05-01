<?php

namespace App\Http\Controllers;

use App\Events\MangoIncome;
use App\Events\OrderCreated;
use App\Events\ClearNotify;
use App\Helpers\GeneralHelper;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Sharoff\Mango\Api\MangoHelper;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\PhoneCode;
use App\Models\Test;



class MangoController extends Controller
{
    public function calling(Request $request): JsonResponse
    {
        //return response()->json($request);
        $url = 'https://app.mango-office.ru/vpbx/commands/callback';

        $data = array(
            "command_id" => "ID" . rand(10000000, 99999999),
            "from" => array(
                "extension" => "101", // внутренний номер, за счет которого производится звонок. (например 101)

                //"number" => "74954887136" // <- кто звонит (можно SIP)
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
        //return response()->json(config('mango.api_key') . $json . config('mango.api_salt'));

        $post = http_build_query($postdata);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return response()->json($response);
    }

    public function call(Request $request): JsonResponse
    {
        //return response()->json($request);
        MangoHelper::setApiKey(config('mango.api_key'))->setApiSalt(config('mango.api_salt'));
        $response = MangoHelper::sendCall($request->ext_number, $request->phone);
        return response()->json($response);
    }

    public function checkPhone2(Request $response)
    {
        //MangoHelper::setApiKey(config('mango.api_key_'.$response))->setApiSalt(config('mango.api_salt'));
        $response = '{
                "to":{
                    "number":"sip:user3@vpbx400210093.mangosip.ru",
                    "acd_group":"100",
                    "extension":"103",
                    "line_number":"84951111111"
                },
                "dct":{
                    "type":0
                },
                "seq":1,
                "from":{
                    "number":"78442297678",
                    "taken_from_call_id":"MToxMDE1OTY4MDoyMjE6Mjg2NTY1MzQyOjE="
                },
                "call_id":"MToxMDE1OTY4MDoyMjE6Mjg2NTY1MzY4",
                "entry_id":"MTI4OTY2MjMzMTk=",
                "location":"abonent",
                "timestamp":1634561740,
                "call_state":"Appeared",
                "disconnect_reason":1121
                }';
        $response = json_decode($response);

        $phone = $response->from->number;

        //$response = MangoHelper::getMethodData();

        $phone = $response->from->number;
        if (strlen($phone) === 11 && $response->call_state === "Appeared" && $response->location === 'abonent' && $response->seq === 1) {
            $def = substr($phone, 1, 3);
            $last = substr($phone, -7, 7);
            $info = PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
            $order = Order::search($phone)->with(['operator', 'region'])->orderBy('retries', 'ASC')->first();

            $data = array(
                "phone" => $phone,
                "phone_region" => $info->region ?? null,
                "region" => $order->region ?? null,
                "line_number" => $response->to->line_number ?? null,
                "client_name" => $order->client_name ?? null,
                "operator" => $order->operator ?? null,
                "work_place" => $response->to->extension ?? null,
                "order_id" => $order->id ?? null,
                "date" => $order->created_at ?? null
            );
            Log::emergency($data);
            MangoIncome::dispatch($data, 8);
        }

    }


    public function checkPhone(Request $request)
    {
        $response = '{
           "entry_id":"232wc3e3w3s222",
           "call_direction":1,
           "from":{
              "number":"79197212620"
           },
           "to":{

           },
           "line_number":"7800123456789",
           "create_time":1399906976,
           "forward_time":1399906978,
           "talk_time":0,
           "end_time":1399906990,
           "entry_result":1,
           "disconnect_reason":1170
        }';

        //MangoHelper::setApiKey(config('mango.api_key'))->setApiSalt(config('mango.api_salt'));
        // $response = MangoHelper::getMethodData();
        $test = new Test();
        $test->name = "summary";
        $test->body = json_encode($response);
        $test->save();
        $response = json_decode($response);

        $phone = $response->from->number;
        if (strlen($phone) === 11 && $response->call_direction === 1 && $response->entry_result === 1) {
            $def = substr($phone, 1, 3);
            $last = substr($phone, -7, 7);
            $info = PhoneCode::with(['region', 'operator'])->where('abc_def', $def)->where('from', '<=', $last)->where('to', '>=', $last)->first();
            $order = Order::search($phone)->with(['operator', 'region'])->latest()->first();
            //return $info;
            if (empty($order)) {
                $order = new Order();
                $order->last_name = "Пропущеный звонок";
                $order->phone = $phone;
                $order->comment = $info->region !==null ? $info->region->name : null;
                $order->save();
                return $order;
            }
            OrderAdded::dispatch($order);
        }


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
