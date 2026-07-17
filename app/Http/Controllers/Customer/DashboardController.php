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

        $cartCount = $user->cartItemsCount();

        return view('customer.dashboard', compact('recentOrders', 'cartCount'));
    }

    /**
     * Halaman produk - menampilkan produk dari Menu Mingguan.
     */
    public function products(Request $request)
    {
        $servicesQuery = CateringService::daily()->where('is_active', true);
        if ($request->filled('service')) {
            $servicesQuery->where('id', $request->service);
        }
        $services = $servicesQuery->get();
        $serviceIds = $services->pluck('id');

        // Gunakan string tanggal hari ini (Y-m-d) dalam zona waktu lokal (Asia/Jakarta)
        // Ini memastikan komparasi di database (SQL) maupun di collection mutlak akurat tanpa bias waktu/UTC
        $todayDateString = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // Ambil jadwal aktif yang belum berakhir (end_date >= hari ini)
        $schedules = \App\Models\MenuPeriod::whereIn('catering_service_id', $serviceIds)
            ->active()
            ->whereDate('end_date', '>=', $todayDateString)
            ->with(['items' => function ($q) use ($todayDateString) {
                // Filter langsung di level query database: hanya ambil menu dengan tanggal >= hari ini (misal >= 2026-07-07)
                // Menu yang tanggalnya sudah lewat (misal 2026-07-06) disembunyikan / tidak dimuat
                $q->whereDate('menu_date', '>=', $todayDateString)
                  ->orderBy('menu_date', 'asc');
            }, 'items.product.cateringService', 'cateringService'])
            ->get();

        // Kumpulkan semua menu items dari jadwal yang valid
        $items = collect();
        foreach ($schedules as $schedule) {
            foreach ($schedule->items as $item) {
                if ($item->menu_date && $item->menu_date->format('Y-m-d') >= $todayDateString) {
                    $items->push($item);
                }
            }
        }
        // Urutkan menu berdasarkan tanggal secara ascending (terdekat ke terjauh)
        $items = $items->sortBy('menu_date')->values();

        return view('customer.products', compact('services', 'schedules', 'items'));
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

        $options = $service->customOptions()->where('type', '!=', 'serving_type')->where('is_active', true)->get();
        $servings = \App\Models\CustomOption::where('type', 'serving_type')->where('is_active', true)->get();
        $customOptions = $options->concat($servings);
        return view('customer.event_custom', compact('service', 'customOptions'));
    }
}
