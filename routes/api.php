<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tenants')->group(function() {
        Route::post('{tenant}/tagihan', [TenantController::class, 'addTagihan'])->name('tenants.addTagihan');
        Route::get('{tenant}/konfirmasi', [TenantController::class, 'konfirmasiPembayaran'])->name('tenants.konfirmasiPembayaran');
        Route::get('{tenant}/perpanjang', [TenantController::class, 'perpanjang'])->name('tenants.perpanjang');
    });

    Route::resources([
        'kosts' => KostController::class,
        'rooms' => RoomController::class,
        'tenants' => TenantController::class,
        'services' => ServiceController::class
    ], [
        'except' => ['create', 'edit']
    ]);
});
