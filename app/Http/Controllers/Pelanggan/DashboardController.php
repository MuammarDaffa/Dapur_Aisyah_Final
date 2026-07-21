<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\LayananKatering;
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
        $servicesQuery = LayananKatering::harian()->where('is_active', true);
        if ($request->filled('service')) {
            $servicesQuery->where('id', $request->service);
        }
        $services = $servicesQuery->get();
        $serviceIds = $services->pluck('id');

        $todayDateString = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // Ambil Menu Harian yang tanggalnya hari ini atau ke depan
        $menus = \App\Models\MenuHarian::whereIn('layanan_katering_id', $serviceIds)
            ->whereNotNull('tanggal')
            ->whereDate('tanggal', '>=', $todayDateString)
            ->with(['layananKatering', 'extras'])
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pelanggan.produk', compact('services', 'menus'));
    }

    /**
     * Halaman pilih layanan acara (Cards)
     */
    public function eventService(LayananKatering $service)
    {
        if (!$service->isAcara()) {
            return redirect()->route('pelanggan.produk')->with('error', 'Layanan tidak valid untuk acara.');
        }

        $pakets = $service->packages()->where('is_active', true)->get();
        return view('pelanggan.acara_layanan', compact('service', 'packages'));
    }

    /**
     * Halaman konfigurasi paket acara
     */
    public function eventPackage(LayananKatering $service, \App\Models\PaketKatering $paket)
    {
        if (!$service->isAcara() || $paket->layanan_katering_id !== $service->id || !$paket->is_active) {
            return redirect()->route('pelanggan.acara.service', $service)->with('error', 'Paket tidak valid.');
        }

        $paket->load(['opsiKustom' => function ($q) {
            $q->where('is_active', true);
        }]);

        return view('pelanggan.acara_paket', compact('service', 'package'));
    }

    /**
     * Halaman konfigurasi custom menu acara
     */
    public function eventCustom(LayananKatering $service)
    {
        if (!$service->isAcara() || !$service->hasFeature('full_custom')) {
            return redirect()->route('pelanggan.acara.service', $service)->with('error', 'Layanan tidak mendukung custom menu.');
        }

        $options = $service->opsiKustom()->where('type', '!=', 'tipe_penyajian')->where('is_active', true)->get();
        $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->where('is_active', true)->get();
        $opsiKustom = $options->concat($servings);
        return view('pelanggan.acara_kustom', compact('service', 'opsiKustom'));
    }
}
