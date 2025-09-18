<?php

use App\Http\Controllers\API\V1\BorrowRoomApiController;
use App\Http\Controllers\HomeController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BorrowingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang
| berisi middleware "web". Buat sesuatu yang hebat!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::post('/borrow', [BorrowingController::class, 'store'])->name('borrow.store');
Route::group([
    'prefix' => 'api/v1',
    'as'     => 'api.v1.'
], function (Router $router) {
    $router->post('borrow-room-with-pegawai', [BorrowRoomApiController::class, 'storeBorrowRoomWithPegawai'])->name('borrow-room-with-pegawai');
});
