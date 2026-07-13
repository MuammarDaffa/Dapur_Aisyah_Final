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

Route::get('/', [LandingController::class, 'index'])->name('landing');

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
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('dashboard')->name('customer.')->group(function () {
    Route::get('/', [CustomerDashboard::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Products
    Route::get('/products', [CustomerDashboard::class, 'products'])->name('products');

    // Event Configurator (Split)
    Route::get('/event/{service}', [CustomerDashboard::class, 'eventService'])->name('event.service');
    Route::get('/event/{service}/package/{package}', [CustomerDashboard::class, 'eventPackage'])->name('event.package');
    Route::get('/event/{service}/custom', [CustomerDashboard::class, 'eventCustom'])->name('event.custom');

    // === Rute dilindungi not_suspended ===
    Route::middleware('not_suspended')->group(function () {
        // Event Cart (masukkan ke keranjang event)
        Route::post('/event/cart', [CartController::class, 'storeEventGroup'])->name('event.cart.store');
        Route::put('/event/cart/{groupId}', [CartController::class, 'updateEventGroup'])->name('event.cart.update');

        // Event Checkout (per group)
        Route::get('/event/checkout/{groupId}', [CheckoutController::class, 'showEventCheckout'])->name('event.checkout.show');
        Route::post('/event/checkout/{groupId}', [CheckoutController::class, 'checkoutEventGroup'])->name('event.checkout.store');

        // Cart (Daily + Event)
        Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

        // Checkout (Daily)
        Route::get('/checkout/{menu_date?}', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout/{menu_date?}', [CheckoutController::class, 'store'])->name('checkout.store');
    });

    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');

    // Reviews
    Route::post('/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');

    // Notifications
    Route::get('/notifications', [CustomerDashboard::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [CustomerDashboard::class, 'markNotificationRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Katering (pusat manajemen layanan)
    Route::resource('catering', CateringController::class);

    // Catering Options CRUD (Menu, Penyajian, Extra — inline dari detail katering)
    Route::post('/catering/{catering}/options', [CateringController::class, 'storeOption'])->name('catering.options.store');
    Route::put('/catering/{catering}/options/{option}', [CateringController::class, 'updateOption'])->name('catering.options.update');
    Route::delete('/catering/{catering}/options/{option}', [CateringController::class, 'destroyOption'])->name('catering.options.destroy');

    // Menu Mingguan (Jadwal Menu — nested under catering)
    Route::get('/catering/{catering}/menu-periods', [MenuPeriodController::class, 'index'])->name('menu-periods.index');
    Route::post('/catering/{catering}/menu-periods', [MenuPeriodController::class, 'store'])->name('menu-periods.store');

    // Products (diakses dari detail katering, bukan standalone)
    Route::resource('products', AdminProductController::class)->except(['index', 'show']);

    // Packages (diakses dari detail katering, bukan standalone)
    Route::resource('packages', PackageController::class)->except(['index', 'show']);

    // Customers
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers');
    Route::get('/customers/{user}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{user}/suspend', [AdminCustomerController::class, 'suspend'])->name('customers.suspend');
    Route::put('/customers/{user}/activate', [AdminCustomerController::class, 'activate'])->name('customers.activate');

    // Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

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
    Route::get('/reviews', [OwnerDashboard::class, 'reviews'])->name('reviews');
    Route::get('/reports', [OwnerDashboard::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| API-like Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/api/villages/{district}', function (\App\Models\District $district) {
        return $district->villages()->get(['id', 'name']);
    })->name('api.villages');

    Route::get('/api/shipping-cost/{district}', function (\App\Models\District $district) {
        return response()->json(['cost' => \App\Models\ShippingCost::getCostByDistrict($district->id)]);
    })->name('api.shipping-cost');

    Route::get('/api/validate-location', function (\Illuminate\Http\Request $request) {
        $result = \App\Services\LocationService::validateLocation(
            $request->input('latitude'),
            $request->input('longitude'),
            $request->input('district_id'),
            $request->input('district_name'),
            $request->input('address')
        );
        return response()->json($result);
    })->name('api.validate-location');

    // API: Custom options per layanan (grouped by type)
    Route::get('/api/service/{service}/custom-options', function (\App\Models\CateringService $service) {
        return $service->customOptions()->where('is_active', true)->get(['id', 'type', 'name', 'price', 'min_qty']);
    })->name('api.service.options');

    // API: Extras per produk
    Route::get('/api/product/{product}/extras', function (\App\Models\Product $product) {
        return $product->extras()->where('is_active', true)->get(['custom_options.id', 'type', 'name', 'price', 'min_qty']);
    })->name('api.product.extras');

    // API: Paket per layanan
    Route::get('/api/service/{service}/packages', function (\App\Models\CateringService $service) {
        return $service->packages()->where('is_active', true)->with('customOptions:id,type,name,price')->get();
    })->name('api.service.packages');

    // API: Detail paket
    Route::get('/api/package/{package}/details', function (\App\Models\CateringPackage $package) {
        $package->load('customOptions:id,type,name,price', 'cateringService:id,name,min_portion,max_portion');
        return response()->json($package);
    })->name('api.package.details');
});
