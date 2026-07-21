<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Pelanggan\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\MenuPeriodController;
use App\Http\Controllers\Pelanggan\ProfilController;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\PembayaranController;
use App\Http\Controllers\Pelanggan\PesananController as CustomerPesananController;
use App\Http\Controllers\Pelanggan\UlasanController as CustomerUlasanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\CateringController;
use App\Http\Controllers\Admin\PaketKateringController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\UlasanController as AdminUlasanController;

use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\PaymentController;

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
| Payment Callback (exclude dari CSRF)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

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

    // Acara Configurator (Split)
    Route::get('/acara/{service}', [CustomerDashboard::class, 'acaraService'])->name('acara.service');
    Route::get('/acara/{service}/package/{package}', [CustomerDashboard::class, 'acaraPackage'])->name('acara.package');
    Route::get('/acara/{service}/custom', [CustomerDashboard::class, 'acaraCustom'])->name('acara.custom');

    // Keranjang Count (untuk badge di navbar, mengembalikan 0 jika guest)
    Route::get('/keranjang/count', [KeranjangController::class, 'count'])->name('keranjang.count');

    // Keranjang Store (masukkan ke keranjang - di-intercept dalam controller jika belum login)
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::post('/acara/keranjang', [KeranjangController::class, 'storeAcaraGroup'])->name('acara.keranjang.store');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('pelanggan.')->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfilController::class, 'update'])->name('profile.update');

    // Acara Keranjang Update & Destroy
    Route::put('/acara/keranjang/{groupId}', [KeranjangController::class, 'updateAcaraGroup'])->name('acara.keranjang.update');

    // Acara Lanjut Ke Pembayaran (per group)
    Route::get('/acara/pembayaran/{groupId}', [PembayaranController::class, 'showAcaraCheckout'])->name('acara.checkout.show');
    Route::post('/acara/pembayaran/{groupId}', [PembayaranController::class, 'checkoutAcaraGroup'])->name('acara.checkout.store');

    // Keranjang Index & Item Operations (Daily)
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::put('/keranjang/{keranjang}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{keranjang}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    // Lanjut Ke Pembayaran (Daily)
    Route::get('/pembayaran/{menu_date?}', [PembayaranController::class, 'index'])->name('checkout');
    Route::post('/pembayaran/{menu_date?}', [PembayaranController::class, 'store'])->name('pembayaran.store');

    // Pesanan
    Route::get('/pesanan', [CustomerPesananController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [CustomerPesananController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/cancel', [CustomerPesananController::class, 'cancel'])->name('pesanan.cancel');

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

    // Pesanan
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

    // Paket Katering (diakses dari detail katering, bukan standalone)
    Route::resource('paket_katering', PaketKateringController::class)->except(['index', 'show']);

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



    Route::get('/api/validate-location', function (\Illuminate\Http\Request $request) {
        $result = \App\Services\LocationService::validateLocation(
            $request->input('latitude'),
            $request->input('longitude'),

            $request->input('district_name'),
            $request->input('address')
        );
        return response()->json($result);
    })->name('api.validate-location');

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

    // API: Paket per layanan
    Route::get('/api/service/{service}/paket_katering', function (\App\Models\LayananKatering $service) {
        return $service->packages()->where('is_active', true)->with('opsiKustom:id,type,name,harga')->get();
    })->name('api.service.packages');

    // API: Detail paket
    Route::get('/api/package/{package}/details', function (\App\Models\PaketKatering $paket) {
        $paket->load('opsiKustom:id,type,name,harga', 'layananKatering:id,name,min_portion,maksimal_porsi');
        return response()->json($paket);
    })->name('api.package.details');
});
