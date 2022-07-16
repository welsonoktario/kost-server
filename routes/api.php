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
use App\Http\Controllers\PembukuanController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\CatatanController;
use App\Http\Controllers\ChatController;

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
        Route::put('{tenant}/password', [TenantController::class, 'gantiPassword'])->name('tenants.gantiPassword');
        Route::get('jatuh-tempo', [TenantController::class, 'jatuhTempo'])->name('tenants.jatuhTempo');
    });

    Route::prefix('tenant-service')->group(function () {
        Route::get('{id}/tenant', [TenantServiceController::class, 'indexById'])->name('tenant-service.indexById');
        Route::post('/', [TenantServiceController::class, 'store'])->name('tenant-service.index');
        Route::put('{id}', [TenantServiceController::class, 'update'])->name('tenant-service.update');
        Route::get('{id}', [TenantServiceController::class, 'index'])->name('tenant-service.index');
    });

    Route::prefix('invoices')->group(function () {
        Route::get('{tenant}/nota', [InvoiceController::class, 'nota'])->name('invoices.nota');
        Route::get('{kost}/history', [InvoiceController::class, 'historyTransaksi'])->name('invoices.history');
    });

    Route::prefix('chats')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chats.index');
        Route::get('{kost}', [ChatController::class, 'chatRooms'])->name('chats.rooms');
        Route::post('{id}', [ChatController::class, 'store'])->name('chats.store');
    });

    Route::resources([
        'catatans' => CatatanController::class,
        'complains' => ComplainController::class,
        'dendas' => DendaController::class,
        'kosts' => KostController::class,
        'notifications' => NotificationController::class,
        'pembukuans' => PembukuanController::class,
        'rooms' => RoomController::class,
        'services' => ServiceController::class,
        'tenants' => TenantController::class,
    ], [
        'except' => ['create', 'edit']
    ]);
});
