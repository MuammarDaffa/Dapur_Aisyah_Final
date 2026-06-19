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
     * Halaman produk - menampilkan produk dari Menu Mingguan (Periode Aktif + Berikutnya).
     */
    public function products(Request $request)
    {
        $services = CateringService::daily()->where('is_active', true)->get();

        // Ambil semua layanan daily yang aktif
        $serviceIds = $services->pluck('id');

        // Periode Aktif (mencakup hari ini)
        $currentPeriods = \App\Models\MenuPeriod::whereIn('catering_service_id', $serviceIds)
            ->active()
            ->current()
            ->with(['items.product.cateringService', 'items.menuPeriod.cateringService', 'cateringService'])
            ->get();

        // Periode Berikutnya (start_date > today), ambil yang paling dekat per layanan
        $upcomingPeriods = \App\Models\MenuPeriod::whereIn('catering_service_id', $serviceIds)
            ->active()
            ->upcoming()
            ->orderBy('start_date')
            ->with(['items.product.cateringService', 'items.menuPeriod.cateringService', 'cateringService'])
            ->get()
            ->unique('catering_service_id');

        // Filter berdasarkan pencarian
        $search = $request->search;
        $serviceFilter = $request->service;

        // Kumpulkan items dari periode aktif
        $currentItems = collect();
        foreach ($currentPeriods as $period) {
            foreach ($period->items as $item) {
                if ($search && !str_contains(strtolower($item->product->name), strtolower($search))) continue;
                if ($serviceFilter && $item->product->catering_service_id != $serviceFilter) continue;
                $currentItems->push($item);
            }
        }

        // Kumpulkan items dari periode berikutnya
        $upcomingItems = collect();
        foreach ($upcomingPeriods as $period) {
            foreach ($period->items as $item) {
                if ($search && !str_contains(strtolower($item->product->name), strtolower($search))) continue;
                if ($serviceFilter && $item->product->catering_service_id != $serviceFilter) continue;
                $upcomingItems->push($item);
            }
        }

        return view('customer.products', compact(
            'services', 'currentPeriods', 'upcomingPeriods',
            'currentItems', 'upcomingItems'
        ));
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
