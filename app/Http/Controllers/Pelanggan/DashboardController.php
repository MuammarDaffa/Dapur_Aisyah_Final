<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Fitur riwayat pesanan (recentOrders) sudah diputus, 
        // sehingga dashboard hanya menampilkan konten umum
        $recentOrders = collect(); 

        return view('pelanggan.dashboard', compact('recentOrders'));
    }

    /**
     * Halaman produk - menampilkan produk dari Menu Mingguan.
     * KARENA ALUR PEMESANAN DITUTUP, HANYA MENAMPILKAN PLACEHOLDER.
     */
    public function produk(Request $request)
    {
        return view('pelanggan.placeholder');
    }

    /**
     * Halaman pilih layanan acara (Cards)
     * KARENA ALUR PEMESANAN DITUTUP, HANYA MENAMPILKAN PLACEHOLDER.
     */
    public function acaraService()
    {
        return view('pelanggan.placeholder');
    }
}
