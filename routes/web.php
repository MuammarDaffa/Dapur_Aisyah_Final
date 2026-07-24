<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Pelanggan\DashboardController as CustomerDashboard;
use App\Http\Controllers\Pelanggan\ProfilController;
use App\Http\Controllers\Pelanggan\UlasanController as CustomerUlasanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\CateringController;
use App\Http\Controllers\Admin\CateringHarianController;
use App\Http\Controllers\Admin\MenuHarianController;
use App\Http\Controllers\Admin\ExtraHarianController;
use App\Http\Controllers\Admin\MenuAcaraController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\UlasanController as AdminUlasanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('landing')->middleware('unverified_customer_redirect');

/*
|--------------------------------------------------------------------------
| Authentication Routes (dari Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Clear Notification Session (AJAX)
|--------------------------------------------------------------------------
*/
Route::post('/session/clear-notification', function (\Illuminate\Http\Request $request) {
    $keys = $request->input('keys', ['info', 'warning', 'success', 'error', 'acara_conflict_error']);
    if (is_array($keys)) {
        $request->session()->forget($keys);
    }
    return response()->json(['status' => 'cleared']);
})->name('session.clear-notification');

/*
|--------------------------------------------------------------------------
| Pelanggan Routes
|--------------------------------------------------------------------------
*/
Route::middleware('unverified_customer_redirect')->prefix('dashboard')->name('pelanggan.')->group(function () {
    Route::get('/produk', [CustomerDashboard::class, 'produk'])->name('produk');
    Route::get('/acara/{service}', [CustomerDashboard::class, 'acaraService'])->name('acara.service');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('pelanggan.')->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/ulasan', [CustomerUlasanController::class, 'store'])->name('ulasan.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.status');
    Route::put('/pesanan/{pesanan}/cancel', [AdminPesananController::class, 'cancel'])->name('pesanan.cancel');
    Route::delete('/pesanan/{pesanan}', [AdminPesananController::class, 'destroy'])->name('pesanan.destroy');

    // =======================================
    // File : routes/web.php
    // Fungsi : Mengatur rute URL untuk Katering.
    // Penambahan 'show' berguna untuk membuka rute halaman detail katering.
    // =======================================
    Route::resource('catering', CateringController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // =======================================
    // Rute Manajemen Katering Harian
    // =======================================
    // 1. Halaman Utama Manajemen Katering Harian
    Route::get('/catering/{layanan}/harian', [CateringHarianController::class, 'index'])->name('catering.harian');
    Route::post('/catering/{layanan}/harian/jadwal', [CateringHarianController::class, 'updateJadwal'])->name('catering.harian.jadwal');

    // 2. CRUD Menu Harian
    Route::get('/catering/{layanan}/menu/create', [MenuHarianController::class, 'create'])->name('menu-harian.create');
    Route::post('/catering/{layanan}/menu', [MenuHarianController::class, 'store'])->name('menu-harian.store');
    Route::get('/menu-harian/{menu}/edit', [MenuHarianController::class, 'edit'])->name('menu-harian.edit');
    Route::put('/menu-harian/{menu}', [MenuHarianController::class, 'update'])->name('menu-harian.update');
    Route::delete('/menu-harian/{menu}', [MenuHarianController::class, 'destroy'])->name('menu-harian.destroy');

    // 3. CRUD Extra Harian
    Route::get('/jadwal-menu/{jadwal}/extra', [ExtraHarianController::class, 'index'])->name('extra-harian.index');
    Route::get('/jadwal-menu/{jadwal}/extra/create', [ExtraHarianController::class, 'create'])->name('extra-harian.create');
    Route::post('/jadwal-menu/{jadwal}/extra', [ExtraHarianController::class, 'store'])->name('extra-harian.store');
    Route::get('/extra-harian/{extra}/edit', [ExtraHarianController::class, 'edit'])->name('extra-harian.edit');
    Route::put('/extra-harian/{extra}', [ExtraHarianController::class, 'update'])->name('extra-harian.update');
    Route::delete('/extra-harian/{extra}', [ExtraHarianController::class, 'destroy'])->name('extra-harian.destroy');

    Route::get('/customers', [AdminPelangganController::class, 'index'])->name('customers');
    Route::get('/ulasan', [AdminUlasanController::class, 'index'])->name('ulasan');
    Route::delete('/ulasan/{ulasan}', [AdminUlasanController::class, 'destroy'])->name('ulasan.destroy');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
    Route::get('/customers', [OwnerDashboard::class, 'customers'])->name('customers');
    Route::get('/ulasan', [OwnerDashboard::class, 'ulasan'])->name('ulasan');
    Route::get('/laporan', [OwnerDashboard::class, 'reports'])->name('reports');
});
