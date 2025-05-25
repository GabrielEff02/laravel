<?php

use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\DriverAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverLocationController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\PhotoController;


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

Route::post('/upload-driver-photo', [PhotoController::class, 'upload']);

Route::post('/absen', [AbsensiController::class, 'absen']);
Route::post('/finish-delivery', [DeliveryController::class, 'finishDelivery']);
Route::post('/start-delivery', [DeliveryController::class, 'startDelivery']);
Route::post('/update-location', [DriverLocationController::class, 'updateLocation']);
Route::get('/locations', [DriverLocationController::class, 'getTodayLocations']);
Route::get('/data', [DeliveryController::class, 'getDeliveryData']);
Route::post('/login', [DriverAuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['message' => 'API route is working']);
});
