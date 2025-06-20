<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth'])->name('/');
Route::get('/dashboard', 'App\Http\Controllers\Web\DashboardController@index')->middleware(['auth']);
// Chart Dashboard
//User Edit
Route::get('/profile', 'App\Http\Controllers\Web\ProfileController@index')->middleware(['auth']);
Route::post('/profile/update', 'App\Http\Controllers\Web\ProfileController@update')->middleware(['auth']);


Route::prefix('master')->middleware('auth')->group(function () {
    $routes = [
        'carousel' => \App\Http\Controllers\Web\Master\CarouselController::class,
        'splash' => \App\Http\Controllers\Web\Master\SplashController::class,
        'brg' => \App\Http\Controllers\Web\Master\BrgController::class,
        'kategori' => \App\Http\Controllers\Web\Master\KategoriController::class,
        'satuan' => \App\Http\Controllers\Web\Master\SatuanController::class,
        'poin' => \App\Http\Controllers\Web\Master\PoinController::class,
        'driver' => \App\Http\Controllers\Web\Master\DriverController::class,
        'compan' => \App\Http\Controllers\Web\Master\CompanController::class,
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
            Route::post(
                '/show/store' . ucfirst($menu) . 'd',
                'store' . ucfirst($menu) . 'd'
            )->name('master.' . $menu . '.show.store' . ucfirst($menu) . 'd');
            Route::get('/show/{id}', 'show')->name('master.' . $menu . '.show');
        });
    }
    Route::get('/driver/resetPassword/{id}', 'App\Http\Controllers\Web\Master\DriverController@resetPassword')->middleware(['auth'])->name('master/driver/resetPassword');
});

Route::prefix('transaksi')->middleware('auth')->group(function () {

    $routes = [
        'jual' => \App\Http\Controllers\Web\Transaksi\JualController::class,
        'tukar' => \App\Http\Controllers\Web\Transaksi\TukarController::class,
        'request' => \App\Http\Controllers\Web\Transaksi\RequestController::class,
    ];


    foreach ($routes as $menu => $controller) {
        Route::prefix($menu)->middleware('auth')->controller($controller)->group(function () use ($menu) {
            Route::get('/', 'index')->name("transaksi.$menu.index");
            Route::get('/create', 'create')->name("transaksi.$menu.create");
            Route::post('/store', 'store')->name("transaksi.$menu.store");

            if ($menu == 'jual' || $menu == 'tukar') {
                Route::post(
                    '/show/store' . ucfirst($menu) . 'd',
                    'store' . ucfirst($menu) . 'd'
                )->name('transaksi.' . $menu . '.show.store' . ucfirst($menu) . 'd');
                Route::get('/show/{id}', 'show')->name('transaksi.' . $menu . '.show');

                Route::post('/update', 'update')->name("transaksi.$menu.update");
                Route::get('/edit', 'edit')->name("transaksi.$menu.edit");

                Route::get('/get-' . $menu . '-kirim', 'get' . ucfirst($menu) . 'Kirim')->name("transaksi.get-" . $menu . '-kirim');
                Route::get('/get-' . $menu . '-ambil', 'get' . ucfirst($menu) . 'Ambil')->name("transaksi.get-" . $menu . '-ambil');
            } else {
                Route::post('/update/{id}', 'update')->name("transaksi.$menu.update");
                Route::get('/edit/{id}', 'edit')->name("transaksi.$menu.edit");
            }

            // route get-{menu} pakai mapping method khusus
            Route::get('/get-' . $menu, 'get' . ucfirst($menu))->name("transaksi.get-" . $menu);
            Route::get('/delete/{id}', 'destroy')->name("transaksi.$menu.delete");
        });
    }
});

require __DIR__ . '/auth.php';
