<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Pelanggan\KateringHarianController;
use App\Http\Controllers\Pelanggan\KateringAcaraController;
use App\Http\Controllers\Pelanggan\ProfilController;
use App\Http\Controllers\Pelanggan\UlasanController as CustomerUlasanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;

use App\Http\Controllers\Admin\CateringHarianController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\PesananController as OwnerPesananController;
use App\Http\Controllers\Owner\PelangganController as OwnerPelangganController;
use App\Http\Controllers\Owner\UlasanController as OwnerUlasanController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use App\Http\Controllers\Owner\AdminAccountController;


// Public Routes
Route::get('/', [LandingController::class, 'index'])->name('landing')->middleware('unverified_customer_redirect');

require __DIR__.'/auth.php';


Route::post('/session/clear-notification', function (\Illuminate\Http\Request $request) {
    $keys = $request->input('keys', ['info', 'warning', 'success', 'error', 'acara_conflict_error']);
    if (is_array($keys)) {
        $request->session()->forget($keys);
    }
    return response()->json(['status' => 'cleared']);
})->name('session.clear-notification');

// pelanggan Routes tanpa login
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

// pelanggan Routes dengan login
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
    Route::post('/katering-harian/bayar/{id}', [KateringHarianController::class, 'bayar'])->name('harian.bayar');
    Route::post('/katering-harian/batalkan/{id}', [KateringHarianController::class, 'batalkanPesanan'])->name('harian.batalkan');
    Route::get('/katering-harian/edit-pesanan/{id}', [KateringHarianController::class, 'editPesanan'])->name('harian.edit_pesanan');
    Route::post('/katering-harian/reschedule/{id}', [KateringHarianController::class, 'reschedule'])->name('harian.reschedule');

    // Pemesanan Katering Acara
    Route::post('/katering-acara/simpan', [KateringAcaraController::class, 'storePesanan'])->name('acara.simpan');
    Route::get('/katering-acara/edit-pesanan/{id}', [KateringAcaraController::class, 'editPesanan'])->name('acara.edit_pesanan');
    Route::get('/katering-acara/detail-pesanan/{id}', [KateringAcaraController::class, 'detailPesanan'])->name('acara.detail_pesanan');
    Route::post('/katering-acara/bayar-dp/{id}', [KateringAcaraController::class, 'bayarDp'])->name('acara.bayar_dp');
    Route::post('/katering-acara/batalkan/{id}', [KateringAcaraController::class, 'batalkanPesanan'])->name('acara.batalkan');

});

// admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.status');
    Route::put('/pesanan/{pesanan}/cancel', [AdminPesananController::class, 'cancel'])->name('pesanan.cancel');
    Route::delete('/pesanan/{pesanan}', [AdminPesananController::class, 'destroy'])->name('pesanan.destroy');
    // 1. Halaman Utama Manajemen Katering Harian (Jadwal)
    Route::get('/catering/harian', [CateringHarianController::class, 'index'])->name('catering.harian');
    Route::post('/catering/harian/jadwal', [CateringHarianController::class, 'updateJadwal'])->name('catering.harian.jadwal');
    Route::delete('/catering/harian/reset', [CateringHarianController::class, 'resetJadwal'])->name('catering.harian.reset');

    // 2. Halaman Detail Katering Acara (Menu dan Minuman)
    Route::get('/catering/acara', [App\Http\Controllers\Admin\CateringAcaraController::class, 'index'])->name('catering.acara');
    Route::post('/catering/acara/stok', [App\Http\Controllers\Admin\CateringAcaraController::class, 'storeStok'])->name('catering.acara.stok');

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


    // 3. CRUD Tambahan Lauk Pauk (Isi Menu / Extra Harian)
    Route::get('/menu/{menu}/tambahan-lauk-pauk', [\App\Http\Controllers\Admin\TambahanLaukPaukController::class, 'index'])->name('menu.tambahan.index');
    Route::post('/menu/{menu}/tambahan-lauk-pauk', [\App\Http\Controllers\Admin\TambahanLaukPaukController::class, 'store'])->name('menu.tambahan.store');
    Route::put('/menu-tambahan/{item}', [\App\Http\Controllers\Admin\TambahanLaukPaukController::class, 'update'])->name('menu.tambahan.update');
    Route::delete('/menu-tambahan/{item}', [\App\Http\Controllers\Admin\TambahanLaukPaukController::class, 'destroy'])->name('menu.tambahan.destroy');

    Route::get('/customers', [AdminPelangganController::class, 'index'])->name('customers');
});

// owner routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
    
    // Pesanan (Read-Only)
    Route::get('/pesanan', [OwnerPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [OwnerPesananController::class, 'show'])->name('pesanan.show');
    
    // Pelanggan (Read-Only)
    Route::get('/customers', [OwnerPelangganController::class, 'index'])->name('customers');
    
    // Ulasan
    Route::get('/ulasan', [OwnerUlasanController::class, 'index'])->name('ulasan');
    Route::delete('/ulasan/{ulasan}', [OwnerUlasanController::class, 'destroy'])->name('ulasan.destroy');
    
    // Laporan
    Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('reports');
    Route::get('/laporan/pdf', [OwnerLaporanController::class, 'cetakPdf'])->name('reports.pdf');

    // Kelola Admin
    Route::get('/admins', [AdminAccountController::class, 'index'])->name('admins.index');
    Route::post('/admins', [AdminAccountController::class, 'store'])->name('admins.store');
    Route::put('/admins/{id}', [AdminAccountController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{id}', [AdminAccountController::class, 'destroy'])->name('admins.destroy');
});


// midtrans webhook
Route::post('/payment/callback', [\App\Http\Controllers\MidtransController::class, 'callback']);
