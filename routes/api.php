<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantServiceController;
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
    Route::prefix('tenants')->group(function () {
        Route::get('{tenant}/konfirmasi', [TenantController::class, 'konfirmasiPembayaran'])->name('tenants.konfirmasiPembayaran');
        Route::get('{tenant}/perpanjang', [TenantController::class, 'perpanjang'])->name('tenants.perpanjang');
        Route::post('{tenant}/tagihan', [TenantController::class, 'addTagihan'])->name('tenants.addTagihan');
    });

    Route::prefix('tenant-service')->group(function () {
        Route::get('{id}/tenant', [TenantServiceController::class, 'indexById'])->name('tenant-service.indexById');
        Route::post('/', [TenantServiceController::class, 'store'])->name('tenant-service.index');
        Route::put('{id}', [TenantServiceController::class, 'update'])->name('tenant-service.update');
        Route::get('{id}', [TenantServiceController::class, 'index'])->name('tenant-service.index');
    });

    Route::resources([
        'complains' => ComplainController::class,
        'kosts' => KostController::class,
        'notifications' => NotificationController::class,
        'rooms' => RoomController::class,
        'services' => ServiceController::class,
        'tenants' => TenantController::class,
    ], [
        'except' => ['create', 'edit']
    ]);
});
