<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()
            ->with('cateringService')
            ->latest()
            ->take(5)
            ->get();

        $cartCount = $user->carts()->count();

        return view('customer.dashboard', compact('recentOrders', 'cartCount'));
    }

    /**
     * Halaman produk - hanya menampilkan produk dari layanan Harian (daily_menu).
     */
    public function products(Request $request)
    {
        $query = Product::with('cateringService')
            ->available()
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }

        if ($request->filled('day')) {
            $query->where('available_days', $request->day);
        }

        $products = $query->paginate(12);
        $services = CateringService::daily()->where('is_active', true)->get();

        $carbonNow = \Carbon\Carbon::now();
        $daysMap = [
            'Sunday' => 'minggu', 'Monday' => 'senin', 'Tuesday' => 'selasa',
            'Wednesday' => 'rabu', 'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu'
        ];
        $currentDay = $daysMap[$carbonNow->format('l')];
        $currentHour = (int) $carbonNow->format('H');

        return view('customer.products', compact('products', 'services', 'currentDay', 'currentHour'));
    }

    /**
     * Halaman pilih layanan event (Cards)
     */
    public function eventService(CateringService $service)
    {
        if (!$service->isEvent()) {
            return redirect()->route('customer.products')->with('error', 'Layanan tidak valid untuk event.');
        }

        $packages = $service->packages()->where('is_active', true)->get();
        return view('customer.event_service', compact('service', 'packages'));
    }

    /**
     * Halaman konfigurasi paket event
     */
    public function eventPackage(CateringService $service, \App\Models\CateringPackage $package)
    {
        if (!$service->isEvent() || $package->catering_service_id !== $service->id || !$package->is_active) {
            return redirect()->route('customer.event.service', $service)->with('error', 'Paket tidak valid.');
        }

        $package->load(['customOptions' => function ($q) {
            $q->where('is_active', true);
        }]);

        return view('customer.event_package', compact('service', 'package'));
    }

    /**
     * Halaman konfigurasi custom menu event
     */
    public function eventCustom(CateringService $service)
    {
        if (!$service->isEvent() || !$service->hasFeature('full_custom')) {
            return redirect()->route('customer.event.service', $service)->with('error', 'Layanan tidak mendukung custom menu.');
        }

        $customOptions = $service->customOptions()->where('is_active', true)->get();
        return view('customer.event_custom', compact('service', 'customOptions'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('customer.notifications', compact('notifications'));
    }

    public function markNotificationRead(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }
}
