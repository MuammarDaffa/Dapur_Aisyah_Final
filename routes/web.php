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
    // =======================================
    // Controllers for Catering/Menu
    // =======================================

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
    Route::get('/profile', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/ulasan', [CustomerUlasanController::class, 'store'])->name('ulasan.store');

    //route pemesanan katering harian map only
    Route::get('/katering-harian/lokasi',[CustomerDashboard::class, 'lokasiHarian'])->name('harian.lokasi');

    // Pemesanan Katering Acara
    Route::get('/katering-acara/lokasi-tanggal',[CustomerDashboard::class, 'lokasiTanggalAcara'])->name('acara.lokasi-tanggal');
    Route::post('/katering-acara/lanjut', [CustomerDashboard::class, 'lanjutAcara'])->name('acara.lanjut');
    Route::get('/katering-acara/pilih-menu', [CustomerDashboard::class, 'pilihMenuAcara'])->name('acara.pilih_menu');
    Route::post('/katering-acara/simpan-menu', [CustomerDashboard::class, 'simpanMenuAcara'])->name('acara.simpan_menu');
    Route::get('/katering-acara/detail-pesanan', [CustomerDashboard::class, 'detailPesanan'])->name('acara.detail_pesanan');
    Route::post('/katering-acara/bayar', [CustomerDashboard::class, 'prosesBayar'])->name('acara.bayar');

        // Rute POST untuk menangkap kiriman data dari form peta
    Route::post('/simpan-lokasi-peta', [CustomerDashboard::class, 'simpanLokasi'])->name('simpan-lokasi-peta');

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
    Route::resource('catering', CateringController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    // =======================================
    // Rute Manajemen Katering Harian & Acara (Menu)
    // =======================================
    // 1. Halaman Utama Manajemen Katering Harian (Jadwal)
    Route::get('/catering/{layanan}/harian', [CateringHarianController::class, 'index'])->name('catering.harian');
    Route::post('/catering/{layanan}/harian/jadwal', [CateringHarianController::class, 'updateJadwal'])->name('catering.harian.jadwal');

    // 2. CRUD Menu
    Route::get('/catering/{layanan}/menu/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('menu.create');
    Route::post('/catering/{layanan}/menu', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menu.destroy');

    // 3. CRUD MenuItem (Isi Menu / Extra Harian)
    Route::get('/menu/{menu}/items', [\App\Http\Controllers\Admin\MenuItemController::class, 'index'])->name('menu.items.index');
    Route::post('/menu/{menu}/items', [\App\Http\Controllers\Admin\MenuItemController::class, 'store'])->name('menu.items.store');
    Route::put('/menu-item/{item}', [\App\Http\Controllers\Admin\MenuItemController::class, 'update'])->name('menu.item.update');
    Route::delete('/menu-item/{item}', [\App\Http\Controllers\Admin\MenuItemController::class, 'destroy'])->name('menu.item.destroy');

    // 4. Hapus rute lama (minuman-acara, dsb) sudah tergabung di menu-item.

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
