<?php

use App\Http\Controllers\GravitelController;
use App\Http\Controllers\JivoWebhookController;
use App\Http\Controllers\MangoCallTrackingController;
use App\Http\Controllers\MangoController;
use App\Http\Controllers\MangoWebhookController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TildaController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//urus

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('dilo', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});
Route::middleware('guest:api')->post('/quiz/{showroom}/{site}', [QuizController::class, 'quiz']);


//mango call tracking
Route::middleware('guest:api')->get('/mango/{showroom}/call-tracking', [MangoCallTrackingController::class, 'execute']);



Route::middleware('guest:api')->post('/jivo/webhook/{showroom_id}/{site_id}', [JivoWebhookController::class, 'handle']);

Route::middleware('guest:api')->post('/mango/{id}/events/call', [MangoWebhookController::class, 'call']);
Route::middleware('guest:api')->post('/mango/{id}/events/summary', [MangoWebhookController::class, 'summary']);
Route::middleware('guest:api')->post('/mango/{id}/events/record/added', [MangoWebhookController::class, 'recordAdded']);

//Route::middleware('guest:api')->post('/events/{id}', [MangoController::class, 'checkPhone2']);
//Route::middleware('guest:api')->post('/gravitel-events/{id}', [GravitelController::class, 'events']);
//Route::middleware('guest:api')->post('/call/{id}', [GravitelController::class, 'call']);;
//Route::middleware('guest:api')->post('/urus-call', [GravitelController::class, 'call']);;
Route::any('{all}', function () {
    return file_get_contents(public_path('_nuxt/index.html'));
})->where('all', '^(?!api).*$');
