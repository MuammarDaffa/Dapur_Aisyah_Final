<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Pelanggan\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\MenuPeriodController;
use App\Http\Controllers\Pelanggan\ProfilController;
use App\Http\Controllers\Pelanggan\UlasanController as CustomerUlasanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\CateringController;
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
    // Produk (Menu Mingguan/Harian)
    Route::get('/produk', [CustomerDashboard::class, 'produk'])->name('produk');

    // Acara Configurator (Placeholder)
    Route::get('/acara/{service}', [CustomerDashboard::class, 'acaraService'])->name('acara.service');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('pelanggan.')->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ulasan
    Route::post('/ulasan', [CustomerUlasanController::class, 'store'])->name('ulasan.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Pesanan (Diblokir di Frontend pelanggan tapi masih bisa dikelola Admin jika ada data tersisa)
    Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.status');
    Route::put('/pesanan/{pesanan}/cancel', [AdminPesananController::class, 'cancel'])->name('pesanan.cancel');
    Route::delete('/pesanan/{pesanan}', [AdminPesananController::class, 'destroy'])->name('pesanan.destroy');

    // Katering (pusat manajemen layanan)
    Route::resource('catering', CateringController::class);

    // Catering Options CRUD (Menu, Penyajian, Extra — inline dari detail katering)
    Route::post('/catering/{catering}/options', [CateringController::class, 'storeOption'])->name('catering.options.store');
    Route::put('/catering/{catering}/options/{option}', [CateringController::class, 'updateOption'])->name('catering.options.update');
    Route::delete('/catering/{catering}/options/{option}', [CateringController::class, 'destroyOption'])->name('catering.options.destroy');

    // Menu Harian (Nested under catering)
    Route::post('/catering/{catering}/menu-harian', [\App\Http\Controllers\Admin\MenuHarianController::class, 'updateBatch'])->name('menu-harian.update-batch');
    
    // Menu Harian Extra
    Route::get('/menu-harian/{menuHarian}/extra', [\App\Http\Controllers\Admin\MenuHarianController::class, 'extraIndex'])->name('menu-harian.extra.index');
    Route::post('/menu-harian/{menuHarian}/extra', [\App\Http\Controllers\Admin\MenuHarianController::class, 'extraStore'])->name('menu-harian.extra.store');
    Route::get('/menu-harian/{menuHarian}/extra/{extra}/edit', [\App\Http\Controllers\Admin\MenuHarianController::class, 'extraEdit'])->name('menu-harian.extra.edit');
    Route::put('/menu-harian/{menuHarian}/extra/{extra}', [\App\Http\Controllers\Admin\MenuHarianController::class, 'extraUpdate'])->name('menu-harian.extra.update');
    Route::delete('/menu-harian/{menuHarian}/extra/{extra}', [\App\Http\Controllers\Admin\MenuHarianController::class, 'extraDestroy'])->name('menu-harian.extra.destroy');

    // Pelanggan
    Route::get('/customers', [AdminPelangganController::class, 'index'])->name('customers');

    // Ulasan
    Route::get('/ulasan', [AdminUlasanController::class, 'index'])->name('ulasan');
    Route::delete('/ulasan/{ulasan}', [AdminUlasanController::class, 'destroy'])->name('ulasan.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
    Route::get('/best-sellers', [OwnerDashboard::class, 'bestSellers'])->name('best-sellers');
    Route::get('/customers', [OwnerDashboard::class, 'customers'])->name('customers');
    Route::get('/ulasan', [OwnerDashboard::class, 'ulasan'])->name('ulasan');
    Route::get('/laporan', [OwnerDashboard::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| API-like Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // API: Custom options per layanan (grouped by type)
    Route::get('/api/service/{service}/custom-options', function (\App\Models\LayananKatering $service) {
        $options = $service->opsiKustom()->where('type', '!=', 'tipe_penyajian')->where('is_active', true)->get(['id', 'type', 'name', 'harga', 'min_qty', 'items']);
        if ($service->isAcara()) {
            $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->where('is_active', true)->get(['id', 'type', 'name', 'harga', 'min_qty', 'items']);
            $options = $options->concat($servings);
        }
        return $options;
    })->name('api.service.options');

    // API: Extras per produk
    Route::get('/api/menu-harian/{menu}/extras', function (\App\Models\MenuHarian $menu) {
        return $menu->extras()->get(['id', 'nama_extra', 'harga']);
    })->name('api.produk.extras');
});
