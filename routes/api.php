<?php

use App\Helpers\ShowroomHelper;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArrivalController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\AuthLogController;
use App\Http\Controllers\AutospotController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\CreditRequestController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataAvController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\Import\ImportCsvController;
use App\Http\Controllers\ImportAgencyController;
use App\Http\Controllers\ImportRequestController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MangoController;
use App\Http\Controllers\MangoEventController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderCountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProstorSmsController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\CarPropertyController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SummaryReportController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TildaController;
use App\Http\Controllers\UdpautoController;
use App\Http\Controllers\Victory\PassAoController;
use App\Http\Controllers\VictoryReportController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShowroomController;
use App\Http\Controllers\UsedCarController;
use App\Http\Controllers\TransitController;
use App\Http\Controllers\CreditNotifyController;
use App\Http\Controllers\ImportVdlController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/test-showroom/{id}/{key}', function ($id, $key) {
    $helper = new ShowroomHelper();

    return response()->json([
        'showroomPair' => $helper->getShowroomPair($id),
        'showroomKey' => $helper->getShowroomKeyByValue($key),
    ]);
});






Route::get('parse-phone', [TestController::class, 'parsePhone'])->name('parsePhone');
Route::get('test-pattern', [TestController::class, 'testPattern'])->name('testPattern');
Route::post('csv-import', [ImportCsvController::class, 'import'])->name('importCsv');

Route::post('report/agency/sites', [ReportController::class, 'bySites']);







Route::get('operators', [ShowroomController::class, 'operators'])->name('operators');
Route::get('orders_open', [OrderController::class, 'orders'])->name('orders');

Route::get('orders/all/{id}', [OrderController::class, 'all_orders'])->name('all_orders');
Route::get('orders/stats/{id}', [OrderController::class, 'stat_orders'])->name('stat_orders');
Route::get('test_search', [TestController::class, 'search'])->name('search_orders');

Route::get('/sales', [CreditRequestController::class, 'sales'])->name('sale-creditrequest');
Route::get('/blacklists', [BlacklistController::class, 'blacklists'])->name('blacklists');

Route::post('blacklists/save', [BlacklistController::class, 'save'])->name('save_blacklist');
Route::post('blacklists/update', [BlacklistController::class, 'update'])->name('update_blacklist');
Route::post('blacklists/delete', [BlacklistController::class, 'delete'])->name('delete_blacklist');



Route::post('report/victory/operator', [VictoryReportController::class, 'operator'])->name('victory-operator');
Route::post('report/operator/victory', [VictoryReportController::class, 'byOperator'])->name('byOperator');
Route::post('report/operator/all-pv', [VictoryReportController::class, 'byOperatorPv'])->name('byOperatorPv');
Route::post('report/status/victory', [VictoryReportController::class, 'byStatus'])->name('byStatus');

Route::post('report/utm', [ReportController::class, 'byUtm']);

Route::post('export/arrive', [OrderController::class, 'exportArrive']);
Route::post('export/no-answer', [OrderController::class, 'export']);
Route::post('report/extend', [ReportController::class, 'extend']);
Route::get('orders/missed-calls/{id}', [OrderController::class, 'missed_calls'])->name('missed_calls');
//Route::get('import_test', [ImportRequestController::class, 'import_test']);
Route::get('temp', [TestController::class, 'read']);
Route::get('get-entry', [TestController::class, 'getEntry']);
Route::get('rest', [OrderCountController::class, 'index'])->name('order_count');
//Route::post('/check_user', [MangoEventController::class, 'testrest']);
Route::get('/test-distribute/{showroom_id}', [TestController::class, 'distribute']);
Route::post('/test-ext', [TestController::class, 'test_ext']);
Route::get('/test-phone4', [TestController::class, 'test_phone4']);
Route::get('/test-calltouch', [TestController::class, 'test_caltouch_orders']);

Route::get('arrivals', [ArrivalController::class, 'arrivals'])->name('arrivals');
Route::post('arrivals/copy', [ArrivalController::class, 'copyArrival'])->name('copy-arrival');


Route::post('report/approval', [ReportController::class, 'approval'])->name('approval');


Route::post('report/operator', [ReportController::class, 'byOperator'])->name('byReportOperator');
Route::post('reports/by-operator', [SummaryReportController::class, 'byOperators'])->name('byOperators');

Route::get('credit-requests', [CreditRequestController::class, 'creditRequests'])->name('credit-requests');

Route::get('test-orders', [OrderController::class, 'test'])->name('test-orders');

//user auth logs
Route::get('user-activities/logs', [AuthLogController::class, 'logs'])->name('auth_logs');

//all reports
Route::post('reports/all', [SummaryReportController::class, 'all'])->name('all_report');
Route::post('reports/operator', [SummaryReportController::class, 'byOperator'])->name('operator_report');
Route::post('reports/arrivals', [SummaryReportController::class, 'arrivals'])->name('arrivals_report');
Route::post('reports/drops', [SummaryReportController::class, 'drops'])->name('drops_report');
Route::post('reports/victory', [SummaryReportController::class, 'victory'])->name('victory_report');
Route::post('reports/agency-sites', [SummaryReportController::class, 'agencySites'])->name('agencySites_report');


// экспорт заявок InHouse
Route::get('/orders/export-inhouse', [OrderController::class, 'inhouseExport'])->name('inhouseExport');

Route::get('projects', [ProjectController::class, 'projects'])->name('projects');
Route::get('project/managers', [ProjectController::class, 'managers'])->name('managers');
Route::get('project/types', [ProjectController::class, 'types'])->name('types');
Route::get('project/systems', [ProjectController::class, 'systems'])->name('systems');
Route::get('project/statuses', [ProjectController::class, 'statuses'])->name('statuses');
Route::post('projects/save', [ProjectController::class, 'storeOrUpdate'])->name('storeOrUpdate');





//Route::get('/import-order', [ImportRequestController::class, 'importByDate']);
Route::get('/import-vdl', [ImportVdlController::class, 'import']);
Route::get('test-ao', [PassAoController::class, 'execute'])->name('execute_');

Route::post('/broadcasting/auth', function (Illuminate\Http\Request $req) {
    if ($req->channel_name == 'cars') {
        return Broadcast::auth($req);
    }
});




//user routes
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->middleware('web');
Route::post('/login', [AuthController::class, 'login'])->middleware('web');
Route::post('sync-user', [PermissionController::class, 'sync']);
//Route::get('check_phone', [OrderController::class, 'check_phone']);

Route::get('/record2/{id}/{recording_id}', [RecordController::class, 'getAudio2']);
Route::get('/record3/{id}/{recording_id}', [RecordController::class, 'getAudio3']);
Route::post('/lead-albato/vk/create', [LeadController::class, 'leadVk']);
Route::post('/lead-albato/{showroom_id}/create', [LeadController::class, 'leadFromVkAds']);
Route::post('/lead-albato/{showroom_id}/rostov-distribute', [LeadController::class, 'vkDistributeRostov']);
Route::post('/lead-albato/{showroom_id}/krim-distribute', [LeadController::class, 'vkDistributeKrim']);

Route::post('lead/autospot/create', [AutospotController::class, 'create'])->middleware('api_token_autospot')->name('create-lead-autospot');

Route::group(['middleware' => ['api_token_2']], function () {
    //leads
    Route::post('lead/create', [LeadController::class, 'create'])->name('create-lead');
    Route::post('lead/pair/distribute', [LeadController::class, 'pairDistribute'])
        ->name('create-lead');
    Route::post('lead/drive/create', [LeadController::class, 'leadDrive'])->name('leadDrive');

});

//tilda leads
Route::group(['middleware' => ['api_token_tilda']], function () {
    Route::post('/tilda', [TildaController::class, 'lead'])->name('tilda-lead');
});

Route::middleware('guest:api')->post('/tilda/{showroom}/{site}', [TildaController::class, 'create']);
Route::middleware('guest:api')->post('/tilda-new/{showroom}/{site}', [TildaController::class, 'createLead']);
Route::middleware('guest:api')->post('/tilda-distribute/{showroom}/{site}', [TildaController::class,
    'createDistribute']);

//udpauto oneplatform
//Route::group(['middleware' => ['api_token_2']], function () {
Route::post('/udpauto', [UdpautoController::class, 'lead'])->name('udpauto-lead');
//});

//leads
Route::group(['middleware' => ['api_token']], function () {
    Route::post('lead/victory/create', [LeadController::class, 'createVictory'])->name('create_victory');


    Route::post('lead/target/create', [TargetController::class, 'leadTarget'])->name('target.create.lead');

    Route::post('lead/{id}/create', [LeadController::class, 'lead'])->name('lead');

    //adding fares from external res
    Route::post('fares/create', [FareController::class, 'create'])->name('fare-create');


    //export new agency uuid
    Route::get('export/orders', [ExportController::class, 'getOrderData']);
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    //get audio from mango cloud
    Route::get('/record/{id}/{recording_id}', [RecordController::class, 'getAudio']);

    Route::post('/users/reset-passwords', [ShowroomController::class, 'resetPasswords']);

    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('web');
    Route::get('/users', [UserController::class, 'users']);
    Route::get('/user-roles', [UserController::class, 'roles']);
    Route::post('/user/update', [UserController::class, 'update']);
    Route::post('/user/save', [UserController::class, 'save']);
    Route::get('/users/by-showroom', [UserController::class, 'usersByShowroom']);
    Route::post('/user/change-online', [UserController::class, 'setOnline']);
    Route::get('/get-user/{id}', [UserController::class, 'getUser']);

    Route::get('/activities', [ActivityController::class, 'car_activities']);
    Route::get('/car-activities', [ActivityController::class, 'car_activities']);
    Route::get('/used-activities', [ActivityController::class, 'used_activities']);

    //car routes
    Route::get('/cars', [CarController::class, 'cars']);
    Route::get('/models', [CarPropertyController::class, 'allModels']);
    Route::get('/models/{mark_id}', [CarPropertyController::class, 'carmodels']);
    Route::post('/car/save', [CarController::class, 'save']);
    Route::get('/car/delete/{id}', [CarController::class, 'delete']);
    Route::get('/enginetypes', [CarPropertyController::class, 'enginetypes']);
    Route::get('/bodytypes', [CarPropertyController::class, 'bodytypes']);
    Route::get('/marks', [CarPropertyController::class, 'carMarks']);
    Route::get('/admin/marks', [AdminCarController::class, 'marks']);
    Route::post('/admin/marks/save', [AdminCarController::class, 'saveMark']);
    Route::post('/admin/marks/update', [AdminCarController::class, 'updateMark']);
    Route::post('/admin/marks/delete', [AdminCarController::class, 'deleteMark']);
    Route::get('/admin/models', [AdminCarController::class, 'models']);
    Route::post('/admin/models/save', [AdminCarController::class, 'saveModel']);
    Route::post('/admin/models/update', [AdminCarController::class, 'updateModel']);
    Route::post('/admin/models/delete', [AdminCarController::class, 'deleteModel']);

    Route::post('/admin/clear-cache', [AuthController::class, 'clearCache']);

    Route::get('/transmissions', [CarPropertyController::class, 'transmissions']);
    Route::get('/wheeltypes', [CarPropertyController::class, 'wheeltypes']);
    Route::get('/statuses', [CarPropertyController::class, 'statuses']);
    Route::get('/showrooms', [ShowroomController::class, 'showrooms']);
    Route::get('/showroom/{id}', [ShowroomController::class, 'showroom']);


    Route::post('/car/transit', [TransitController::class, 'transit']);
    Route::post('/car/transit/approve', [TransitController::class, 'approveTransit']);
    Route::post('/car/transit/cancel', [TransitController::class, 'cancelTransit']);
    Route::get('/car/transits', [TransitController::class, 'transits']);


    //showroom

    Route::get('/managers', [ShowroomController::class, 'managers']);
    Route::get('/managers2', [ShowroomController::class, 'second_managers']);

    //operators
    Route::post('/operator/save', [ShowroomController::class, 'saveOperator']);

    Route::get('/work-places', [ShowroomController::class, 'work_places']);


    //used-car routes
    Route::get('/used-cars', [UsedCarController::class, 'cars']);


    Route::post('/used-car/transit', [TransitController::class, 'usedTransit']);
    Route::post('/used-car/transit/approve', [TransitController::class, 'approveUsedTransit']);
    Route::post('/used-car/transit/cancel', [TransitController::class, 'cancelUsedTransit']);
    Route::get('/used-car/transits', [TransitController::class, 'usedcar_transits']);
    Route::post('/used-car/histories', [UsedCarController::class, 'histories']);


    Route::post('/used-car/save', [UsedCarController::class, 'save']);
    Route::post('/used-car/delete', [UsedCarController::class, 'delete']);


    //shipment
    Route::get('/car/shipments', [ShipmentController::class, 'shipments']); //;
    Route::get('/shipment/companies', [ShipmentController::class, 'companies']);
    Route::get('/shipment/drivers', [ShipmentController::class, 'drivers']);
    Route::get('/shipment/statuses', [ShipmentController::class, 'statuses']);
    Route::post('/shipment/save', [ShipmentController::class, 'save']);
    Route::get('/shipment/statuses', [ShipmentController::class, 'statuses']);
    Route::get('/shipment/detail/{id}', [ShipmentController::class, 'detail']); //;
    Route::get('/shipment/delete/{id}', [ShipmentController::class, 'delete']); //;
    Route::post('/shipment/car/add', [ShipmentController::class, 'addCar']); //;
    Route::post('/shipment/used-car/add', [ShipmentController::class, 'addUsedCar']); //;


    //arrivals

    Route::get('arrivals/all', [ArrivalController::class, 'all_arrivals'])->name('all_arrivals');
    Route::post('arrivals/save', [ArrivalController::class, 'saveArrival'])->name('saveArrival');
    Route::post('arrivals/to-arrive', [ArrivalController::class, 'to_arrive'])->name('to_arrive');
    Route::post('arrivals/to-credit', [ArrivalController::class, 'to_credit'])->name('to_credit');
    Route::get('arrivals/{id}', [ArrivalController::class, 'arrivals'])->name('arrivals_id');

    //orders
    Route::get('orders', [OrderController::class, 'orders'])->name('main_orders');
    Route::get('deffered-orders', [OrderController::class, 'deffered_orders'])->name('main_deffered_orders');

    Route::get('light-orders', [OrderController::class, 'light_orders'])->name('light_orders');

    Route::post('order/pass-orders', [OrderController::class, 'passOrders'])->name('passOrders');

    Route::get('orders/arrivals/{id}', [OrderController::class, 'all_arrivals'])->name('all_arrivals_id');

    //Route::get('testrest', [OrderController::class, 'findModel'])->name('findModel');
    Route::post('orders/save', [OrderController::class, 'saveOrder'])->name('save');
    Route::post('orders/update', [OrderController::class, 'update'])->name('update');
    Route::post('orders/copy', [OrderController::class, 'copy'])->name('copy');
    Route::post('orders/defer-purchase', [OrderController::class, 'deferPurchase'])->name('deferPurchase');
    Route::post('orders/delete-deferred-item', [OrderController::class, 'deleteDeferred'])->name('delete-deferred-item');
    Route::post('orders/return-defer-purchase', [OrderController::class, 'returnPurchased'])->name('return-deferred-item');

    Route::post('orders/distribute', [OrderController::class, 'distribute'])->name('distribute');
    Route::post('orders/repeats', [OrderController::class, 'repeats'])->name('repeats');
    Route::post('orders/all-repeats', [OrderController::class, 'all_repeats'])->name('all_repeats');
    Route::post('orders/update_mini', [OrderController::class, 'update_mini'])->name('update_mini');
    Route::post('orders/visited-mini', [OrderController::class, 'visited_mini'])->name('visited_mini');
    Route::get('orders/histories', [OrderController::class, 'histories'])->name('histories');

    Route::prefix('sms')->group(function () {
        Route::get('templates', [SmsTemplateController::class, 'index']);
        Route::post('templates', [SmsTemplateController::class, 'store']);
        Route::delete('templates/{id}', [SmsTemplateController::class, 'destroy']);
    });

    Route::post('orders/blacklist', [OrderController::class, 'blacklist'])->name('blacklist');
    Route::post('orders/blacklist-request', [OrderController::class, 'blacklistRequest'])->name('blacklist-request');
    Route::get('orders/{id}', [OrderController::class, 'orders'])->name('orders_id');
    Route::get('order/delete/{id}', [OrderController::class, 'delete'])->name('delete');
    Route::get('order/{showroom}/detail/{id}', [OrderController::class, 'detail'])->name('detail');
    Route::get('order/trashes', [OrderController::class, 'trashes'])->name('trashes');
    Route::get('order/drops', [OrderController::class, 'drops'])->name('drops');
    Route::get('order/arrival-statuses', [OrderController::class, 'arrival_statuses'])->name('drops');
    Route::get('order-types', [OrderController::class, 'types'])->name('order_types');
    Route::get('order-statuses', [OrderController::class, 'statuses'])->name('order_statuses');


    //dashboard
    Route::get('dashboard/arrivals', [DashboardController::class, 'index'])->name('getInfo');
    Route::post('dashboard/orders', [DashboardController::class, 'orders'])->name('getOrders');
    Route::get('dashboard/credits', [DashboardController::class, 'creditRequests'])->name('getCredits');
    Route::get('dashboard/used-cars', [DashboardController::class, 'used_cars'])->name('used_cars');
    Route::get('dashboard/cars', [DashboardController::class, 'cars'])->name('getCars');


    //credit-requests




    Route::post('credit-requests/save', [CreditRequestController::class, 'saveCreditRequest'])->name('save-creditrequest');

    Route::post('credit-requests/delete', [CreditRequestController::class, 'deleteCreditRequests'])->name('delete-creditrequests');
    Route::post('credit-requests/sale', [CreditRequestController::class, 'saleCreditRequest'])->name('sold-credit');
    Route::post('credit-requests/restore', [CreditRequestController::class, 'restore'])->name('restore-creditrequest');
    Route::post('credit-requests/send-notify', [CreditNotifyController::class, 'sendNotify'])->name('sendNotify');
    Route::post('credit-requests/notify-manager', [CreditNotifyController::class, 'notifyManager'])->name('notifyManager');
    Route::post('credit-requests/sync', [CreditRequestController::class, 'syncCreditRequest'])->name('sync-credit');

    //showroom

    Route::get('banks', [ShowroomController::class, 'banks'])->name('banks');
    Route::get('regions', [ShowroomController::class, 'regions'])->name('regions');
    Route::get('sites', [ShowroomController::class, 'sites'])->name('sites');
    Route::get('site-types', [ShowroomController::class, 'site_types'])->name('site_types');
    Route::post('site/save', [ShowroomController::class, 'saveSite'])->name('save_site');
    Route::post('site/update', [ShowroomController::class, 'updateSite'])->name('update_site');
    Route::post('site/duplicate', [ShowroomController::class, 'duplicateSite'])->name('duplicate_site');
    Route::post('site/delete', [ShowroomController::class, 'deleteSite'])->name('delete_site');
    Route::get('admin-sites', [ShowroomController::class, 'admin_sites'])->name('admin_sites');
    Route::get('agencies', [ShowroomController::class, 'agencies'])->name('agencies');


    Route::get('preparers', [ShowroomController::class, 'preparers'])->name('preparers');


    //role and permission
    Route::get('permissions', [PermissionController::class, 'abilities']);
    Route::post('permission/create', [PermissionController::class, 'create']);
    Route::get('permission/allow', [PermissionController::class, 'allow']);
    Route::get('permission/disallow', [PermissionController::class, 'disallow']);
    Route::get('permission/retract', [PermissionController::class, 'retract']);
    Route::get('permission/forbid', [PermissionController::class, 'forbid']);
    Route::get('user-permissions/{id}', [PermissionController::class, 'user_permissions']);
    Route::get('roles', RoleController::class);
    Route::get('operator/roles',  [RoleController::class, 'operator_roles']);

    Route::get('get-tabs', [PageController::class, 'getTabs']);
    Route::get('pages', [PageController::class, 'pages']);

    Route::get('test', function () {
        //$role = Role::create(['name' => 'writer']);
        $user = User::where('id', 4)->first();
        //$items = Bouncer::assign('director')->to($user);
        $items = $user->getAbilities();
        ///$user->givePermissionTo('see_tab_operators');
        //$permission = Permission::create(['name' => 'see_tab_operators']);
        // Bouncer::allow($user)->to(['see_tab_light', 'see_tab_avrora', 'see_tab_avangard', 'see_tab_atlant', 'see_tab_tablet/1', 'see_tab_tablet/2'], User::class)
        return response()->json($items);
    });


    //admin
    Route::get('schedules', [OperatorController::class, 'schedules'])->name('schedules');
    Route::post('schedules/create', [OperatorController::class, 'createSchedule'])->name('create-schedule');
    Route::post('schedules/update', [OperatorController::class, 'updateSchedule'])->name('update-schedule');
    Route::post('schedules/delete', [OperatorController::class, 'deleteSchedule'])->name('delete-schedule');
    Route::post('crm/workplace', [CrmController::class, 'setWorkPlace'])->name('setWorkPlace');


    Route::get('programmatic-schedules', [OperatorController::class, 'programmaticSchedules'])->name('schedules');
    Route::post('programmatic-schedules/create', [OperatorController::class, 'createProgrammaticSchedule'])->name('create-schedule');
    Route::post('programmatic-schedules/update', [OperatorController::class, 'updateProgrammaticSchedule'])->name('update-schedule');
    Route::post('programmatic-schedules/delete', [OperatorController::class, 'deleteProgrammaticSchedule'])->name('delete-schedule');


    Route::get('test/shedule', [OrderController::class, 'shedule'])->name('shedule');

    //fares
    Route::get('fares', [FareController::class, 'fares'])->name('fares');
    Route::post('fares/save', [FareController::class, 'save'])->name('fare-save');

    Route::post('fares/delete', [FareController::class, 'delete'])->name('delete-fare');
    Route::post('fares/upload', [FareController::class, 'upload'])->name('upload-fare');



    //report



    Route::post('report/status', [ReportController::class, 'byStatus']);
    Route::post('report/sites', [ReportController::class, 'bySite']);
    Route::post('report-victory/sites', [VictoryReportController::class, 'bySite']);
    Route::post('report/source', [ReportController::class, 'bySource']);
    Route::post('report/content', [ReportController::class, 'byContent']);
    Route::post('report/campaign', [ReportController::class, 'byCampaign']);
    Route::post('report/medium', [ReportController::class, 'byMedium']);
    Route::post('report/term', [ReportController::class, 'byTerm']);
    Route::post('report/date', [ReportController::class, 'byDates']);


    Route::post('report/sites-agency', [ReportController::class, 'bySiteAgency']);

    Route::post('report/agency/payment', [ReportController::class, 'payment']);

    Route::post('report/agency/target-website-webhook', [ReportController::class, 'bySitesWebhook']);
    Route::post('report/agency/dates', [ReportController::class, 'byDates']);
    Route::post('report/agency', [ReportController::class, 'byAgency']);
    Route::post('report/drops', [ReportController::class, 'drops']);


    //prostor sms
    Route::post('/prostor/sms/send', [ProstorSmsController::class, 'sendSms'])->name('prosotor-sen-sms');



    //victory






    ///Route::post('crm/import', [ImportRequestController::class, 'import'])->name('import-requests');

    //Route::post('/events/{id}', [MangoController::class, 'checkPhone2']);
    Route::post('clear-notify', [MangoController::class, 'clearNotify']);
    Route::post('call', [MangoEventController::class, 'makeCall'])->name('makeCall');
    Route::post('checkPhone2', [MangoController::class, 'checkPhone2'])->name('checkPhone2');
    //Route::get('testrest', [OrderController::class, 'test']);
    Route::post('request-report', [DataAvController::class, 'report']);
    Route::post('number', [DataAvController::class, 'number']);
});
