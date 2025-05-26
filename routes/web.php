<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth']);
Route::get('/dashboard', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth']);
// Chart Dashboard
//User Edit
Route::get('/profile', 'App\Http\Controllers\Web\ProfileController@index')->middleware(['auth']);
Route::post('/profile/update', 'App\Http\Controllers\Web\ProfileController@update')->middleware(['auth']);


Route::prefix('master')->middleware('auth')->group(function () {
    $routes = [
        'brg' => \App\Http\Controllers\Web\Master\BrgController::class,
        'poin' => \App\Http\Controllers\Web\Master\PoinController::class,
        'driver' => \App\Http\Controllers\Web\Master\DriverController::class,
    ];
    foreach ($routes as $menu => $controller) {

        Route::prefix($menu)->middleware('auth')->controller($controller)->group(function () use ($menu) {
            Route::get('/', 'index')->name("master.$menu.index");
            Route::get('/create', 'create')->name("master.$menu.create");
            Route::post('/store', 'store')->name("master.$menu.store");
            Route::get('/edit/{id}', 'edit')->name("master.$menu.edit");
            Route::post('/update/{id}', 'update')->name("master.$menu.update");
            Route::get('/delete/{id}', 'destroy')->name("master.$menu.delete");
            Route::get('/get-' . $menu, 'get' . ucwords($menu))->name("master.get-" . $menu);
            if ($menu == 'brg' || $menu == 'poin') {
                Route::post(
                    '/show/store' . ucfirst($menu) . 'd',
                    'store' . ucfirst($menu) . 'd'
                )->name('master.' . $menu . '.show.store' . ucfirst($menu) . 'd');
                Route::get('/show/{id}', 'show')->name('master.' . $menu . '.show');
            }
        });
    }
    Route::get('/driver/resetPassword/{id}', 'App\Http\Controllers\Web\Master\DriverController@resetPassword')->middleware(['auth'])->name('master/driver/resetPassword');
});

Route::prefix('transaksi')->middleware('auth')->group(function () {
    $routes = [
        'jual' => \App\Http\Controllers\Web\Transaksi\JualController::class,
    ];
    foreach ($routes as $menu => $controller) {

        Route::prefix($menu)->middleware('auth')->controller($controller)->group(function () use ($menu) {
            Route::get('/', 'index')->name("transaksi.$menu.index");
            Route::get('/create', 'create')->name("transaksi.$menu.create");
            Route::post('/store', 'store')->name("transaksi.$menu.store");
            Route::get('/edit/{id}', 'edit')->name("transaksi.$menu.edit");
            Route::post('/update/{id}', 'update')->name("transaksi.$menu.update");
            Route::get('/delete/{id}', 'destroy')->name("transaksi.$menu.delete");
            Route::get('/get-' . $menu, 'get' . ucwords($menu))->name("transaksi.get-" . $menu);
            if ($menu == 'jual' || $menu == 'poin') {
                Route::post(
                    '/show/store' . ucfirst($menu) . 'd',
                    'store' . ucfirst($menu) . 'd'
                )->name('transaksi.' . $menu . '.show.store' . ucfirst($menu) . 'd');
                Route::get('/show/{id}', 'show')->name('transaksi.' . $menu . '.show');
            }
        });
    }
});
require __DIR__ . '/auth.php';
