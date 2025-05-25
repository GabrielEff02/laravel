<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth']);
Route::get('/dashboard', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth']);
// Chart Dashboard
Route::get('/chart', 'App\Http\Controllers\Web\ChartController@chart')->middleware(['auth'])->middleware(['checkDivisi:programmer,owner,assistant']);
Route::get('/cheatsheet', 'App\Http\Controllers\Web\CheatsheetController@index')->middleware(['auth'])->middleware(['checkDivisi:programmer']);
//User Edit
Route::get('/profile', 'App\Http\Controllers\Web\ProfileController@index')->middleware(['auth']);
Route::post('/profile/update', 'App\Http\Controllers\Web\ProfileController@update')->middleware(['auth']);
Route::get('/user/photo/{id}', function ($username) {
    $user = \App\Models\User::findOrFail($username);
    if ($user->profile_picture) {
        $finfo = finfo_open();
        $mime = finfo_buffer($finfo, $user->profile_picture, FILEINFO_MIME_TYPE);
        return response($user->profile_picture)->header('Content-Type', $mime);
    }
    abort(404);
})->name('user.photo'); // ← ini nama routenya


// Periode
Route::post('/periode', 'App\Http\Controllers\PeriodeController@index')->middleware(['auth'])->name('periode');

// Barang
Route::get('/brg', 'App\Http\Controllers\Web\BrgController@index')->middleware(['auth'])->name('brg');
Route::post('/brg/store', 'App\Http\Controllers\Web\BrgController@store')->middleware(['auth'])->name('brg/store');
Route::post('/brg/show/storeBrgd', 'App\Http\Controllers\Web\BrgController@storeBrgd')->middleware(['auth'])->name('brg/show/storeBrgd');
Route::get('/brg/show/{brg}', 'App\Http\Controllers\Web\BrgController@show')->middleware(['auth'])->name('brg/show');
Route::get('/brg/create', 'App\Http\Controllers\Web\BrgController@create')->middleware(['auth'])->name('brg/create');
Route::get('/brg/edit/{brg}', 'App\Http\Controllers\Web\BrgController@edit')->middleware(['auth'])->name('brg/edit');
Route::post('/brg/update/{brg}', 'App\Http\Controllers\Web\BrgController@update')->middleware(['auth'])->middleware(['auth'])->name('brg/update');
Route::get('/get-brg', 'App\Http\Controllers\Web\BrgController@getBrg')->middleware(['auth'])->name('get-brg');
Route::get('/brg/delete/{brg}', 'App\Http\Controllers\Web\BrgController@destroy')->middleware(['auth'])->name('brg.delete');

// Produk Penukaran Point
Route::get('/poin', 'App\Http\Controllers\Web\PoinController@index')->middleware(['auth'])->name('poin');
Route::post('/poin/store', 'App\Http\Controllers\Web\PoinController@store')->middleware(['auth'])->name('poin/store');
Route::post('/poin/show/storePoind', 'App\Http\Controllers\Web\PoinController@storePoind')->middleware(['auth'])->name('poin/show/storePoind');
Route::get('/poin/show/{poin}', 'App\Http\Controllers\Web\PoinController@show')->middleware(['auth'])->name('poin/show');
Route::get('/poin/create', 'App\Http\Controllers\Web\PoinController@create')->middleware(['auth'])->name('poin/create');
Route::get('/poin/edit/{poin}', 'App\Http\Controllers\Web\PoinController@edit')->middleware(['auth'])->name('poin/edit');
Route::post('/poin/update/{poin}', 'App\Http\Controllers\Web\PoinController@update')->middleware(['auth'])->middleware(['auth'])->name('poin/update');
Route::get('/get-poin', 'App\Http\Controllers\Web\PoinController@getPoin')->middleware(['auth'])->name('get-poin');
Route::get('/poin/delete/{poin}', 'App\Http\Controllers\Web\PoinController@destroy')->middleware(['auth'])->name('poin.delete');

// driver
Route::get('/driver', 'App\Http\Controllers\Web\DriverController@index')->middleware(['auth'])->name('driver');
Route::post('/driver/store', 'App\Http\Controllers\Web\DriverController@store')->middleware(['auth'])->name('driver/store');
Route::get('/driver/resetPassword/{driver}', 'App\Http\Controllers\Web\DriverController@resetPassword')->middleware(['auth'])->name('driver/resetPassword');
Route::get('/driver/create', 'App\Http\Controllers\Web\DriverController@create')->middleware(['auth'])->name('driver/create');
Route::get('/driver/edit/{driver}', 'App\Http\Controllers\Web\DriverController@edit')->middleware(['auth'])->name('driver/edit');
Route::put('/driver/update/{driver}', 'App\Http\Controllers\Web\DriverController@update')->middleware(['auth'])->middleware(['auth'])->name('driver/update');
Route::get('/get-driver', 'App\Http\Controllers\Web\DriverController@getDriver')->middleware(['auth'])->name('get-driver');
Route::get('/driver/delete/{driver}', 'App\Http\Controllers\Web\DriverController@destroy')->middleware(['auth'])->name('driver.delete');

require __DIR__ . '/auth.php';
