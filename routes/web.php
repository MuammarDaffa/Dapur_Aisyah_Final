<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Pelanggan\KateringHarianController;
use App\Http\Controllers\Pelanggan\KateringAcaraController;
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
| Public Pelanggan Routes (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware('unverified_customer_redirect')->prefix('katering')->name('pelanggan.')->group(function () {
    // Katering Harian
    Route::get('/harian/lokasi', [KateringHarianController::class, 'showFormLokasi'])->name('harian.lokasi');
    Route::post('/harian/lokasi', [KateringHarianController::class, 'storeSessionLokasi'])->name('harian.simpan_lokasi');
    Route::get('/harian/menu', [KateringHarianController::class, 'showMenu'])->name('harian.menu');

    // Katering Acara
    Route::get('/acara/lokasi', [KateringAcaraController::class, 'showFormLokasi'])->name('acara.lokasi');
    Route::post('/acara/lokasi', [KateringAcaraController::class, 'storeSessionLokasi'])->name('acara.simpan_lokasi');
    Route::get('/acara/menu', [KateringAcaraController::class, 'showMenu'])->name('acara.menu');
    
    // Legacy route deleted
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('pelanggan.')->group(function () {
    Route::get('/profile', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/ulasan', [CustomerUlasanController::class, 'store'])->name('ulasan.store');
    Route::get('/riwayat-pesanan', [ProfilController::class, 'riwayatPesanan'])->name('riwayat');
    Route::delete('/pesanan/{id}/hapus', [ProfilController::class, 'hapusPesanan'])->name('pesanan.hapus');
    Route::post('/pelunasan/{id}', [KateringAcaraController::class, 'prosesPelunasan'])->name('pelunasan');
    // Pemesanan Katering Harian
    Route::post('/katering-harian/simpan', [KateringHarianController::class, 'storePesanan'])->name('harian.simpan');
    Route::get('/katering-harian/detail-pesanan/{id}', [KateringHarianController::class, 'detailPesanan'])->name('harian.detail_pesanan');
    Route::post('/katering-harian/batalkan/{id}', [KateringHarianController::class, 'batalkanPesanan'])->name('harian.batalkan');
    Route::get('/katering-harian/edit-pesanan/{id}', [KateringHarianController::class, 'editPesanan'])->name('harian.edit_pesanan');
    Route::post('/katering-harian/reschedule/{id}', [KateringHarianController::class, 'reschedule'])->name('harian.reschedule');

    // Pemesanan Katering Acara
    Route::post('/katering-acara/simpan', [KateringAcaraController::class, 'storePesanan'])->name('acara.simpan');
    Route::get('/katering-acara/edit-pesanan/{id}', [KateringAcaraController::class, 'editPesanan'])->name('acara.edit_pesanan');
    Route::post('/katering-acara/simpan-menu', [KateringAcaraController::class, 'simpanMenuAcara'])->name('acara.simpan_menu');
    Route::get('/katering-acara/detail-pesanan/{id}', [KateringAcaraController::class, 'detailPesanan'])->name('acara.detail_pesanan');
    Route::post('/katering-acara/bayar-dp/{id}', [KateringAcaraController::class, 'bayarDp'])->name('acara.bayar_dp');
    Route::post('/katering-acara/batalkan/{id}', [KateringAcaraController::class, 'batalkanPesanan'])->name('acara.batalkan');

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
    // CRUD Layanan dihapus, diganti hardcode tipe_layanan

    // =======================================
    // Rute Manajemen Katering Harian & Acara (Menu)
    // =======================================
    // 1. Halaman Utama Manajemen Katering Harian (Jadwal)
    Route::get('/catering/harian', [CateringHarianController::class, 'index'])->name('catering.harian');
    Route::post('/catering/harian/jadwal', [CateringHarianController::class, 'updateJadwal'])->name('catering.harian.jadwal');

    // 2. Halaman Detail Katering Acara (Menu dan Minuman)
    Route::get('/catering/acara', [App\Http\Controllers\Admin\CateringAcaraController::class, 'index'])->name('catering.acara');

    // 2. CRUD Menu
    Route::get('/catering/{tipe_layanan}/menu/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('menu.create');
    Route::post('/catering/{tipe_layanan}/menu', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menu.destroy');

    // CRUD Minuman (Khusus Acara)
    Route::post('/catering/{tipe_layanan}/minuman', [\App\Http\Controllers\Admin\MinumanController::class, 'store'])->name('minuman.store');
    Route::put('/minuman/{minuman}', [\App\Http\Controllers\Admin\MinumanController::class, 'update'])->name('minuman.update');
    Route::delete('/minuman/{minuman}', [\App\Http\Controllers\Admin\MinumanController::class, 'destroy'])->name('minuman.destroy');


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

/*
|--------------------------------------------------------------------------
| Webhook Midtrans
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [\App\Http\Controllers\MidtransController::class, 'callback']);
