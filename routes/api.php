<?php

use App\Http\Controllers\Api\DeliveryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/delivery/upload-driver-photo', [DeliveryController::class, 'upload']);
Route::post('/delivery/absen', [DeliveryController::class, 'absen']);
Route::post('/delivery/finish-delivery', [DeliveryController::class, 'finishDelivery']);
Route::post('/delivery/start-delivery', [DeliveryController::class, 'startDelivery']);
Route::post('/delivery/update-location', [DeliveryController::class, 'updateLocation']);
Route::get('/delivery/locations', [DeliveryController::class, 'getTodayLocations']);
Route::get('/delivery/data', [DeliveryController::class, 'getDeliveryData']);
Route::post('/delivery/login', [DeliveryController::class, 'login']);
