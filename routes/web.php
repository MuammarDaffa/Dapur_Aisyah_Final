<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\MenuPeriodController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CateringController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\ReportController;
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
    $keys = $request->input('keys', ['info', 'warning', 'success', 'error', 'event_conflict_error']);
    if (is_array($keys)) {
        $request->session()->forget($keys);
    }
    return response()->json(['status' => 'cleared']);
})->name('session.clear-notification');

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware('unverified_customer_redirect')->prefix('dashboard')->name('customer.')->group(function () {
    // Produk (Menu Mingguan/Harian)
    Route::get('/produk', [CustomerDashboard::class, 'produk'])->name('produk');

    // Event Configurator (Split)
    Route::get('/event/{service}', [CustomerDashboard::class, 'eventService'])->name('event.service');
    Route::get('/event/{service}/package/{package}', [CustomerDashboard::class, 'eventPackage'])->name('event.package');
    Route::get('/event/{service}/custom', [CustomerDashboard::class, 'eventCustom'])->name('event.custom');

    // Keranjang Count (untuk badge di navbar, mengembalikan 0 jika guest)
    Route::get('/keranjang/count', [CartController::class, 'count'])->name('keranjang.count');

    // Keranjang Store (masukkan ke keranjang - di-intercept dalam controller jika belum login)
    Route::post('/keranjang', [CartController::class, 'store'])->name('keranjang.store');
    Route::post('/event/keranjang', [CartController::class, 'storeEventGroup'])->name('event.keranjang.store');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('customer.')->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Event Keranjang Update & Destroy
    Route::put('/event/keranjang/{groupId}', [CartController::class, 'updateEventGroup'])->name('event.keranjang.update');

    // Event Checkout (per group)
    Route::get('/event/checkout/{groupId}', [CheckoutController::class, 'showEventCheckout'])->name('event.checkout.show');
    Route::post('/event/checkout/{groupId}', [CheckoutController::class, 'checkoutEventGroup'])->name('event.checkout.store');

    // Keranjang Index & Item Operations (Daily)
    Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');
    Route::put('/keranjang/{keranjang}', [CartController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{keranjang}', [CartController::class, 'destroy'])->name('keranjang.destroy');

    // Checkout (Daily)
    Route::get('/checkout/{menu_date?}', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/{menu_date?}', [CheckoutController::class, 'store'])->name('checkout.store');

    // Pesanan
    Route::get('/pesanan', [CustomerOrderController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [CustomerOrderController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/cancel', [CustomerOrderController::class, 'cancel'])->name('pesanan.cancel');

    // Ulasan
    Route::post('/ulasan', [CustomerReviewController::class, 'store'])->name('ulasan.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Pesanan
    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('pesanan');
    Route::get('/pesanan/{pesanan}', [AdminOrderController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{pesanan}/status', [AdminOrderController::class, 'updateStatus'])->name('pesanan.status');
    Route::put('/pesanan/{pesanan}/cancel', [AdminOrderController::class, 'cancel'])->name('pesanan.cancel');
    Route::delete('/pesanan/{pesanan}', [AdminOrderController::class, 'destroy'])->name('pesanan.destroy');

    // Katering (pusat manajemen layanan)
    Route::resource('catering', CateringController::class);

    // Catering Options CRUD (Menu, Penyajian, Extra — inline dari detail katering)
    Route::post('/catering/{catering}/options', [CateringController::class, 'storeOption'])->name('catering.options.store');
    Route::put('/catering/{catering}/options/{option}', [CateringController::class, 'updateOption'])->name('catering.options.update');
    Route::delete('/catering/{catering}/options/{option}', [CateringController::class, 'destroyOption'])->name('catering.options.destroy');

    // Menu Mingguan (Jadwal Menu — nested under catering)
    Route::get('/catering/{catering}/menu-periods', [MenuPeriodController::class, 'index'])->name('menu-periods.index');
    Route::post('/catering/{catering}/menu-periods', [MenuPeriodController::class, 'store'])->name('menu-periods.store');

    // Produk (diakses dari detail katering, bukan standalone)
    Route::resource('produk', AdminProductController::class)->except(['index', 'show']);

    // Packages (diakses dari detail katering, bukan standalone)
    Route::resource('packages', PackageController::class)->except(['index', 'show']);

    // Customers
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers');

    // Ulasan
    Route::get('/ulasan', [AdminReviewController::class, 'index'])->name('ulasan');
    Route::delete('/ulasan/{ulasan}', [AdminReviewController::class, 'destroy'])->name('ulasan.destroy');

    // Shipping
    Route::resource('shipping', ShippingController::class)->except(['show']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
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
    Route::get('/reports', [OwnerDashboard::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| API-like Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/api/desa/{kecamatan}', function (\App\Models\Kecamatan $kecamatan) {
        return $kecamatan->desa()->get(['id', 'name']);
    })->name('api.desa');

    Route::get('/api/shipping-cost/{kecamatan}', function (\App\Models\Kecamatan $kecamatan) {
        return response()->json(['cost' => \App\Models\OngkosKirim::getCostByDistrict($kecamatan->id)]);
    })->name('api.shipping-cost');

    Route::get('/api/validate-location', function (\Illuminate\Http\Request $request) {
        $result = \App\Services\LocationService::validateLocation(
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('kecamatan_id'),
            $request->input('district_name'),
            $request->input('address')
        );
        return response()->json($result);
    })->name('api.validate-location');

    // API: Custom options per layanan (grouped by type)
    Route::get('/api/service/{service}/custom-options', function (\App\Models\LayananKatering $service) {
        $options = $service->opsiKustom()->where('type', '!=', 'tipe_penyajian')->where('is_active', true)->get(['id', 'type', 'name', 'harga', 'min_qty', 'items']);
        if ($service->isEvent()) {
            $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->where('is_active', true)->get(['id', 'type', 'name', 'harga', 'min_qty', 'items']);
            $options = $options->concat($servings);
        }
        return $options;
    })->name('api.service.options');

    // API: Extras per produk
    Route::get('/api/produk/{produk}/extras', function (\App\Models\Produk $produk) {
        return $produk->extras()->where('is_active', true)->get(['opsi_kustom.id', 'type', 'name', 'harga', 'min_qty']);
    })->name('api.produk.extras');

    // API: Paket per layanan
    Route::get('/api/service/{service}/packages', function (\App\Models\LayananKatering $service) {
        return $service->packages()->where('is_active', true)->with('opsiKustom:id,type,name,harga')->get();
    })->name('api.service.packages');

    // API: Detail paket
    Route::get('/api/package/{package}/details', function (\App\Models\PaketKatering $package) {
        $package->load('opsiKustom:id,type,name,harga', 'layananKatering:id,name,min_portion,maksimal_porsi');
        return response()->json($package);
    })->name('api.package.details');
});
