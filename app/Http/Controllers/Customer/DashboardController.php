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
            $query->whereJsonContains('available_days', $request->day);
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
     * Halaman konfigurasi pesanan event.
     */
    public function eventConfigurator(CateringService $service)
    {
        // Pastikan ini adalah katering event
        if (!$service->isEvent()) {
            return redirect()->route('customer.products')->with('error', 'Layanan tidak valid untuk event.');
        }

        $packages = $service->packages()->with(['customOptions' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        $customOptions = $service->customOptions()->where('is_active', true)->get();

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
