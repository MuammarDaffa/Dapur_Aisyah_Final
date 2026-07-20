<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\LayananKatering;
use App\Models\Produk;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentOrders = $user->pesanan()
            ->with('layananKatering')
            ->latest()
            ->take(5)
            ->get();

        $cartCount = $user->cartItemsCount();

        return view('pelanggan.dashboard', compact('recentOrders', 'cartCount'));
    }

    /**
     * Halaman produk - menampilkan produk dari Menu Mingguan.
     */
    public function produk(Request $request)
    {
        $servicesQuery = LayananKatering::daily()->where('is_active', true);
        if ($request->filled('service')) {
            $servicesQuery->where('id', $request->service);
        }
        $services = $servicesQuery->get();
        $serviceIds = $services->pluck('id');

        // Gunakan string tanggal hari ini (Y-m-d) dalam zona waktu lokal (Asia/Jakarta)
        // Ini memastikan komparasi di database (SQL) maupun di collection mutlak akurat tanpa bias waktu/UTC
        $todayDateString = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // Ambil jadwal aktif yang belum berakhir (end_date >= hari ini)
        $schedules = \App\Models\PeriodeMenu::whereIn('layanan_katering_id', $serviceIds)
            ->active()
            ->whereDate('end_date', '>=', $todayDateString)
            ->with(['items' => function ($q) use ($todayDateString) {
                // Filter langsung di level query database: hanya ambil menu dengan tanggal >= hari ini (misal >= 2026-07-07)
                // Menu yang tanggalnya sudah lewat (misal 2026-07-06) disembunyikan / tidak dimuat
                $q->whereDate('menu_date', '>=', $todayDateString)
                  ->orderBy('menu_date', 'asc');
            }, 'items.produk.layananKatering', 'layananKatering'])
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

        return view('pelanggan.produk', compact('services', 'schedules', 'items'));
    }

    /**
     * Halaman pilih layanan event (Cards)
     */
    public function eventService(LayananKatering $service)
    {
        if (!$service->isEvent()) {
            return redirect()->route('pelanggan.produk')->with('error', 'Layanan tidak valid untuk event.');
        }

        $pakets = $service->packages()->where('is_active', true)->get();
        return view('pelanggan.acara_layanan', compact('service', 'packages'));
    }

    /**
     * Halaman konfigurasi paket event
     */
    public function eventPackage(LayananKatering $service, \App\Models\PaketKatering $paket)
    {
        if (!$service->isEvent() || $paket->layanan_katering_id !== $service->id || !$paket->is_active) {
            return redirect()->route('pelanggan.event.service', $service)->with('error', 'Paket tidak valid.');
        }

        $paket->load(['opsiKustom' => function ($q) {
            $q->where('is_active', true);
        }]);

        return view('pelanggan.acara_paket', compact('service', 'package'));
    }

    /**
     * Halaman konfigurasi custom menu event
     */
    public function eventCustom(LayananKatering $service)
    {
        if (!$service->isEvent() || !$service->hasFeature('full_custom')) {
            return redirect()->route('pelanggan.event.service', $service)->with('error', 'Layanan tidak mendukung custom menu.');
        }

        $options = $service->opsiKustom()->where('type', '!=', 'tipe_penyajian')->where('is_active', true)->get();
        $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->where('is_active', true)->get();
        $opsiKustom = $options->concat($servings);
        return view('pelanggan.acara_kustom', compact('service', 'opsiKustom'));
    }
}
