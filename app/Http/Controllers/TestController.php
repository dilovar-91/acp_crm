<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dilo;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meilisearch\Endpoints\Indexes;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestController extends Controller
{
    public function testrest(Request $request)
    {
        //return $request;

        $ot = Carbon::now();
        $do = Carbon::now();


        $ot->hour = 19;
        $ot->minute = 00;
        $ot->second = 00;

        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
        }
        return Order::where('status_id', 1000)->where('showroom_id', $request->id)->phone($request->phone)->latest()->first();
        return User::where('work_place', $request->extension)->where('showroom_id', $request->id)->latest('updated_at')->first();
        return Order::search($request->phone)->with(['operator', 'region', 'status'])->where('showroom_id', $request->id)->orderBy('retries', 'ASC')->first();
        return Order::where('operator_id', 1000)->where('showroom_id', $request->id)->phone($request->phone)->latest()->first();


        $ot = Carbon::now();
        $do = Carbon::now();


        $ot->hour = 19;
        $ot->minute = 00;
        $ot->second = 00;

        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
        }

        $user = User::where('id', $request->user_id)->whereHas('schedule', function ($query) use ($today) {
            $query->where($today, 1);
        })
            ->first();
        return !empty($user);

        $from->hour = 19;
        $from->minute = 00;
        $from->second = 00;
        $to->hour = 19;
        $to->minute = 00;
        $to->second = 00;
        $res = User::with(['schedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])
            ->whereIn('role_id', [2, 6])
            ->where('showroom_id', $request->showroom_id)
            ->whereHas('schedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->withCount([
                'orders' => function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to])->whereNull('retries');
                }
            ])
            ->orderBy('orders_count')
            ->get();
        return $res;


        $record = ActivityLog::with(['record' => function ($query) {
            $query->where('description', '8');
        }])->whereHas('record')->orderBy('id', "desc")->paginate(25);

        return $record;


        //return Site::where('phone', $request->line_number)->orWhere('second_phone', $request->line_number)->orWhere('phone', 'like',  '%'.$request->line_number.'%')->latest()->first();
        //$res = DB::table('tests')->whereJsonContains( "body->to->number",  "79381030178")->get();
        //$res = ActivityLog::where( "id", '>=', 2086677)->where( "description",  "record")->get();
        /*- recording_id: идентификатор записи разговора;
        - action: разрешенные значения download, play;
        - vpbx_api_key: ключ API;
        - timestamp: timestamp (часовой пояс UTC+3), время до которого действует ссылка;
        - sign: подпись, рассчитанная по формуле sign=sha256(vpbx_api_key + timestamp +
                    recording_id + vpbx_api_salt)*/

        //return $res;

        /*foreach ($res as $item){
            //return $item->properties;
            $api_key = config('mango.api_key_' . $item->properties['showroom_id']);
            $salt_key = config('mango.api_salt_' . $item->properties['showroom_id']);
            $record_id = $item->properties['recording_id'];
            $timestamp = Carbon::now()->addDays(1)->timestamp;
            $sign = hash('sha256', $api_key . $timestamp . $record_id . $salt_key);
            $link = "https://app.mango-office.ru/vpbx/queries/recording/link/".$record_id."/play/".$api_key."/".$timestamp."/".$sign;
            echo "<p><a href='".$link."'>".$link."</a></p>";
        }*/
        //return 123;
        //order =  Order::where('operator_id', 1000)->where('showroom_id', 8)->where('phone','LIKE', '%79272550741%')->latest()->first();
        //return $order ?? 'empty';

        //$logs = ActivityLog::with('record')->where('description', 8)->orderBy('id', 'DESC')->paginate(15);
        //return $logs;

        $dayName = Carbon::now()->format('l');
        return strtolower($dayName);
        $operator = User::where('work_place', $request->id)->where('showroom_id', $request->id)->whereHas('operatorSchedule', function ($query) use ($dayName) {
            $query->where(strtolower($dayName), "1");
        })->latest()->first();
        return $operator;
    }

    public function distribute($showroom_id)
    {
        $from = null;
        $to = null;

        $ot = Carbon::now();
        $do = Carbon::now();
        if ($showroom_id === 9) {
            $ot->hour = 18;
            $ot->minute = 30;
        } else {
            $ot->hour = 19;
            $ot->minute = 00;
        }
        $ot->second = 00;
        $do->hour = 23;
        $do->minute = 59;
        $do->second = 59;

        $check = Carbon::now()->between($ot, $do);
        if ($check == 1) {
            $from = Carbon::now();
            $to = Carbon::now()->addDays(1);
            $today = Str::lower(Carbon::now()->addDays(1)->englishDayOfWeek);
        } else {
            $today = Str::lower(Carbon::now()->englishDayOfWeek);
            $from = Carbon::now()->subDays(1);
            $to = Carbon::now();
        }

        if ($showroom_id === 9) {
            $from->hour = 18;
            $from->minute = 30;
            $to->hour = 18;
            $to->minute = 30;
        } else {
            $from->hour = 19;
            $from->minute = 00;
            $to->hour = 19;
            $to->minute = 00;
        }
        $from->second = 00;
        $to->second = 00;


        $res = User::with(['schedule' => function ($query) use ($today) {
            $query->where($today, 1);
        }])
            ->whereIn('role_id', [2, 6])
            ->where('showroom_id', $showroom_id)
            ->whereHas('schedule', function ($query) use ($today) {
                $query->where($today, 1);
            })
            ->withCount([
                'orders' => function ($query) use ($from, $to) {
                    $query->leftJoin('sites', 'orders.site_id', '=', 'sites.id')
                        ->where(function ($subquery) {
                            $subquery->where('sites.type_id', '<>', 4)
                                ->orWhereNull('sites.type_id');
                        })
                        ->where(function ($subquery) {
                            $subquery->whereNull('orders.site_id')
                                ->orWhereNotNull('orders.site_id'); // Include orders with site_id = NULL
                        })
                        ->whereBetween('orders.created_at', [$from, $to])
                        ->whereNull('orders.retries');
                }
            ])
            ->orderBy('orders_count')
            ->get();
        return $res ?? null;
    }

    public function test_ext(Request $request)
    {

        $ext = $request->ext;
        $showroom_id = $request->showroom_id;
        $dayName = Carbon::now()->format('l');

        $user = User::where('work_place', $ext)->where('showroom_id', $showroom_id)->latest('updated_at')->first();
        return $user;
        $user = User::where('work_place', $ext)->where('showroom_id', $showroom_id)->whereHas('operatorSchedule', function ($query) use ($dayName) {
            $query->where(strtolower($dayName), "1");
        })->latest()->first();


    }

    public function test_caltouch_orders(Request $request)
    {
        return Order::with(['site', 'operator', 'status', 'type'])
            ->whereHas('site', function ($query) {
                $query->where('agency_id', 14)
                    ->whereNotNull('calltouch_site_id')
                    ->whereNotNull('calltouch_access_key');
            })
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get()
            ->groupBy('site_id');


    }
    public function test_phone4(Request $request)
    {
        $showroom_id = $request->showroom_id;

        $phone ="+79111435123";


        //Log::error('phone-' . $phone);

        $res = Order::phone4($phone)
            ->where('showroom_id', $showroom_id)
            ->get();


        $query = Order::phone4($phone)
            ->where('showroom_id', $showroom_id);
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        Log::info('SQL: ' . $sql);
        Log::info('Bindings: ', $bindings);

        return $res;


        $order = Order::phone3($phone)->with(['operator', 'region', 'status'])->where('showroom_id', $showroom_id)->orderBy('retries', 'DESC')->first();

        if (empty($order)) {
            return "empty";
        }

        return $order;


    }

    public function read()
    {

        //$fileContents = Storage::get('laravel.log');
        $logFilePath = "C:\OSPanel\domains\accas.loc\storage\app\laravel.log";

        //$logFilePath = '/path/to/your/log/file.log';
        $outputLines = [];
        $pattern = '/accas\.ru\r\n\r\\"(.*?)\\"\}\]/';
        $file = fopen($logFilePath, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                if (strpos($line, 'production.ERROR: Unauthorized api_token') !== false) {
                    if (preg_match_all($pattern, $line, $matches)) {
                        echo $matches[1] . "<hr>";
                    }

                }
            }
            fclose($file);
        }
        return;

        // Print or process the output lines
        foreach ($outputLines as $line) {
            echo $line;
        }


        $content = Storage::get('laravel.log');
        $lines = explode(PHP_EOL, $content);

        $searchText = 'Unauthorized api_token';

        // Loop through each line
        foreach ($lines as $line) {
            // Check if the line contains the search text
            if (strpos($line, $searchText) !== false) {
                // Line contains the search text
                $start = 'a-c77.ru';
                $end = '"}]';

                $startPos = strpos($line, $start);
                $endPos = strpos($line, $end, $startPos);

                if ($startPos !== false && $endPos !== false) {
                    $text = substr($line, $startPos + strlen($start), $endPos - ($startPos + strlen($start)));
                    $results[] = $text;
                }
            }
        }
        return $results;
        $results = [];

        return count($lines);

        foreach ($lines as $line) {
            $start = 'a-c77.ru';
            $end = '"}]';

            $startPos = strpos($line, $start);
            $endPos = strpos($line, $end, $startPos);

            if ($startPos !== false && $endPos !== false) {
                $text = substr($line, $startPos + strlen($start), $endPos - ($startPos + strlen($start)));
                $results[] = $text;
            }
        }
        return $results;
        $pattern = '/\r\nHost(.*?)\}/s';
        preg_match_all($pattern, $fileContents, $matches);
        $texts = $matches[1];

        // Do something with the extracted texts
        foreach ($texts as $text) {
            return $text . "\n";
        }

    }

    public function getEntry(Request $request)
    {
        //Log::emergency("(job) handled". $this->entry_id);
        $activity = ActivityLog::where('entry_id', $request->entry_id)->latest('created_at')->first();

        if (!empty($activity)) {
            return $activity;
        } else {
            return $activity;
        }

    }


    public function test_orders(Request $request)
    {
        $keyword = $request->fio;
        $names = explode(' ', $keyword);
        $namePlaceholders = rtrim(str_repeat('?,', count($names)), ',');
        //return $names;
        $results = DB::table('orders')
            ->whereRaw("client_name LIKE CONCAT('%', $namePlaceholders, '%')", $names)
            ->orderByRaw("CASE WHEN client_name IN ('" . implode("','", $names) . "') THEN 0 ELSE 1 END")
            ->get();

        return $results;

        $results = DB::table('orders')
            ->where('showroom_id', 10)
            ->where(function ($query) use ($names) {
                $query->whereIn('client_name', $names);
            })
            ->orderByRaw("CASE WHEN client_name IN ('" . implode("','", $names) . "') THEN 0 ELSE 1 END")
            ->get();
        return $results;
    }


    public function exportOrdersToExcel(Request $request)
    {

        $order = $request->order ?? 'created_at';


        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([AllowedFilter::exact('showroom_id'), AllowedFilter::exact('site_id'), AllowedFilter::exact('copied'), AllowedFilter::exact('type_id'), AllowedFilter::exact('payment_method'), AllowedFilter::exact('status_id'), AllowedFilter::exact('drop_id'), AllowedFilter::exact('trash_id'), AllowedFilter::exact('operator_id'), AllowedFilter::exact('region_id'), AllowedFilter::exact('mark_id'), AllowedFilter::exact('model_id'), AllowedFilter::scope('between'), AllowedFilter::scope('betweenUpdated'), AllowedFilter::scope('agency'), AllowedFilter::scope('betweenArrive'), AllowedFilter::scope('repeat'), AllowedFilter::scope('betweenArrived'), AllowedFilter::scope('betweenArrived'), AllowedFilter::scope('betweenCallback'), AllowedFilter::scope('LastCall'), AllowedFilter::scope('search'), AllowedFilter::scope('tell'), AllowedFilter::exact('arrived'), AllowedFilter::exact('utm_campaign'), AllowedFilter::scope('notConfirmed')])->with(['region', 'site', 'operator', 'showroom', 'type', 'status', 'mark', 'model', 'source', 'trash', 'drop']);;


        if (!array_key_exists('search', (array)$request->filter)) {
            $orders->orderBy($order, 'DESC');
        } else {
            $orders->orderBy('created_at', 'DESC');
        }


        $orders = $orders->get();

        // Create a new Excel file
        $filename = 'orders.xlsx';
        $filePath = storage_path($filename);

        // Create a new Excel writer object


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Orders');

        // Set headers
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Сайт');
        $sheet->setCellValue('B1', 'Тип');
        $sheet->setCellValue('C1', 'Состояние заявки');
        $sheet->setCellValue('D1', 'Регион');
        $sheet->setCellValue('E1', 'Дата создания');
        $sheet->setCellValue('F1', 'Марка и модель');
        $sheet->setCellValue('G1', 'Клиент');
        $sheet->setCellValue('H1', 'Сотовый');
        $sheet->setCellValue('I1', 'Приедет');
        $sheet->setCellValue('J1', 'Повторы');
        $sheet->setCellValue('K1', 'Дата изменения');
        $sheet->setCellValue('L1', 'Перезвонить');
        $sheet->setCellValue('M1', 'Комментарий');
        $sheet->setCellValue('N1', 'Общие комментарии');

        // Populate data
        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, (@$order->site->title));
            $sheet->setCellValue('B' . $row, (@$order->type->name));
            $sheet->setCellValue('C' . $row, (@$order->status->name));
            $sheet->setCellValue('D' . $row, (@$order->region->name));
            $sheet->setCellValue('E' . $row, Carbon::parse($order->created_at)->format('d.m.Y'));
            $sheet->setCellValue('F' . $row, (@$order->mark->name . " " . @$order->model->name . " " . $order->complectation));
            $sheet->setCellValue('G' . $row, $order->client_name);
            $sheet->setCellValue('H' . $row, $order->phone);
            $sheet->setCellValue('I' . $row, Carbon::parse($order->will_arrive)->format('d.m.Y'));
            $sheet->setCellValue('J' . $row, (@$order->retries));
            $sheet->setCellValue('K' . $row, Carbon::parse($order->updated_at)->format('d.m.Y'));
            $sheet->setCellValue('L' . $row, Carbon::parse($order->callback)->format('d.m.Y'));
            $sheet->setCellValue('M' . $row, @$order->comment);
            $sheet->setCellValue('N' . $row, @$order->general_comment);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Return the file as a download
        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }


    public function dilo(Request $request)
    {
        $sort = $request->sort;
        $created_after = $request->created_after;
        $customers = Dilo::search($request->term, function (Indexes $meiliSearch, string $query, array $options) use ($sort, $created_after) {

            return $meiliSearch->search($query);
        });

        return $customers->get();
    }


    public function search(Request $request)
    {

        $user = Auth::user() ?? User::find(6);
        $per_page = 10;
        //$order = $request->order ?? 'created_at';


        DB::enableQueryLog();
        //$order3 = Order::where('showroom_id', 7)->phone3('+79082991425')->latest()->first();

        $allowedFilters = [
            AllowedFilter::exact('showroom_id'),
            AllowedFilter::exact('site_id'),
            AllowedFilter::exact('copied'),
            AllowedFilter::exact('type_id'),
            AllowedFilter::exact('payment_method'),
            AllowedFilter::exact('status_id'),
            AllowedFilter::exact('drop_id'),
            AllowedFilter::exact('trash_id'),
            AllowedFilter::exact('operator_id'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::exact('mark_id'),
            AllowedFilter::exact('model_id'),
            AllowedFilter::exact('arrived'),
            AllowedFilter::exact('utm_campaign'),
            AllowedFilter::scope('between'),
            AllowedFilter::scope('betweenUpdated'),
            AllowedFilter::scope('agency'),
            AllowedFilter::scope('betweenArrive'),
            AllowedFilter::scope('repeat'),
            AllowedFilter::scope('betweenArrived'),
            AllowedFilter::scope('betweenArrived'),
            AllowedFilter::scope('betweenCallback'),
            AllowedFilter::scope('LastCall'),
            AllowedFilter::scope('search'),
            AllowedFilter::scope('search3'),
            AllowedFilter::scope('phone3'),
            AllowedFilter::scope('searchFio'),
            AllowedFilter::scope('searchFion'),
            AllowedFilter::scope('searchClient'),
            AllowedFilter::scope('searchComment'),
            AllowedFilter::scope('searchTelephone'),
            AllowedFilter::scope('tell'),
            AllowedFilter::scope('notConfirmed')
        ];


        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters($allowedFilters);


        $orders = $orders->paginate($per_page);

        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            Log::info('SQL query: ' . $query['query'], ['time' => $query['time']]);
        }

        return response()->json($orders, 200);
        if ($user->role_id === 2 && (array_key_exists('betweenCallback', (array)$request->filter) || array_key_exists('betweenArrive', (array)$request->filter))) {
            $orders->where('operator_id', $user->id);
        } else if ($user->role_id === 2 && !array_key_exists('search', (array)$request->filter) && !array_key_exists('between', (array)$request->filter)) {
            $orders->where('operator_id', $user->id);
        }
        if (!array_key_exists('search', (array)$request->filter)) {
            $orders->orderBy($order, 'DESC');
        } else {
            $orders->orderBy('created_at', 'DESC');
        }
        $allowedRoleIds = [1, 3];

        if (!in_array($user->role_id, $allowedRoleIds)) {
            $orders->whereNotIn('status_id', [9, 10]);
        }
        $orders = $orders->paginate($per_page);
        return response()->json($orders, 200);
    }


    public function parsePhone(Request $request)
    {
        try {
            $phone = PhoneNumber::make($request->phone, 'RU')->formatE164();
        } catch (Throwable $e) {
            $phone = preg_replace('/[^0-9+]/', '', $request->phone) ?? null;
        }

        return $phone;
    }


    public function testPattern()
    {
        $pattern = '/^\+?7(96517[01]|96723[23]|963609|963610|965390|965416|965415|927846|906775|909631|967057|967060)\d{4}$/';

        // Тестовые номера: валидные и невалидные
        $testNumbers = [
            // ✅ Валидные (должны проходить)
            '+79651701234' => 'OK',
            '79651701234'   => 'OK',
            '+79651719876'  => 'OK',
            '79651719876'   => 'OK',
            '+79672320000'  => 'OK',
            '79672320000'   => 'OK',
            '+79672334455'  => 'OK',
            '79672334455'   => 'OK',
            '+79636091122'  => 'OK',
            '79636091122'   => 'OK',
            '+79636109988'  => 'OK',
            '79636109988'   => 'OK',
            '+79653901122'  => 'OK',
            '79653901122'   => 'OK',
            '+79654161122'  => 'OK',
            '79654161122'   => 'OK',
            '+79654151122'  => 'OK',
            '79654151122'   => 'OK',
            '+79278461122'  => 'OK',
            '79278461122'   => 'OK',
            '+79067751122'  => 'OK',
            '79067751122'   => 'OK',
            '+79096311122'  => 'OK',
            '79096311222'   => 'OK',
            '+79670571122'  => 'OK',
            '79670571122'   => 'OK',
            '+79670601122'  => 'OK',
            '79670601122'   => 'OK',

            // ❌ Невалидные (должны отклоняться)
            '89651701234'   => 'FAIL', // не начинается с 7 или +7
            '+89651701234'  => 'FAIL',
            '79651721234'   => 'FAIL', // 172 — не в [01]
            '79672311234'   => 'FAIL', // 231 — не 232/233
            '79636081234'   => 'FAIL', // 608 — не 609
            '79636111234'   => 'FAIL', // 611 — не 610
            '79653911234'   => 'FAIL', // 391 — не 390
            '79654171234'   => 'FAIL', // 417 — не 415/416
            '79278451234'   => 'FAIL', // 845 — не 846
            '79067741234'   => 'FAIL', // 774 — не 775
            '79096301234'   => 'FAIL', // 630 — не 631
            '79670561234'   => 'FAIL', // 056 — не 057
            '79670611234'   => 'FAIL', // 061 — не 060
            '+7965170123'   => 'FAIL', // 10 цифр (не хватает)
            '+796517012345' => 'FAIL', // 12 цифр (слишком много)
            '9651701234'    => 'FAIL', // нет 7 в начале
            ''              => 'FAIL',
            '+7'            => 'FAIL',
        ];

        echo "Проверка маски: $pattern\n\n";

        foreach ($testNumbers as $number => $expected) {
            $result = preg_match($pattern, $number) ? 'OK' : 'FAIL';
            $status = $result === $expected ? '✅' : '❌';
            printf("%-15s — %s %s (ожидалось: %s)<br>\n", $number, $result, $status, $expected);
        }
    }
}
