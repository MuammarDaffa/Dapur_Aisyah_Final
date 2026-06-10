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
        $query = Product::active()->with('cateringService')
            ->whereHas('cateringService', function ($q) {
                $q->whereJsonContains('available_features', 'daily_menu');
            });

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }

        if ($request->filled('day')) {
            $query->whereJsonContains('available_days', $request->day);
        }

        $products = $query->paginate(12);

        // Filter layanan harian saja untuk dropdown filter
        $services = CateringService::active()->daily()->get();

        return view('customer.products', compact('products', 'services'));
    }

    /**
     * Halaman konfigurasi pesanan event.
     */
    public function eventConfigurator(CateringService $service)
    {
        // Pastikan layanan adalah tipe event
        if (!$service->isEvent()) {
            return redirect()->route('customer.products')
                ->with('error', 'Layanan ini bukan layanan event.');
        }

        $packages = $service->packages()
            ->active()
            ->with('customOptions')
            ->get();

        $customOptions = $service->customOptions()
            ->active()
            ->get();

        return view('customer.event_configurator', compact('service', 'packages', 'customOptions'));
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
