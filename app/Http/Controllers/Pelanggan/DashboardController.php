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
        return view('pelanggan.lokasi_harian');
    }

    /**
     * Halaman pilih layanan acara (Cards)
     * KARENA ALUR PEMESANAN DITUTUP, HANYA MENAMPILKAN PLACEHOLDER.
     */
    public function acaraService()
    {
        return view('pelanggan.lokasi_acara');
    }


        /**
     * Halaman Pilih Lokasi untuk Katering Harian (Tanpa Datepicker)
     */
    public function lokasiHarian()
    {
        return view('pelanggan.lokasi_harian');
    }

    /**
     * Halaman Pilih Lokasi & Tanggal untuk Katering Acara (Dengan Datepicker)
     */
    public function lokasiTanggalAcara()
    {
        return view('pelanggan.lokasi_acara');
    }

    /**
     * Placeholder action untuk tombol Lanjut di Halaman Acara
     */
    public function lanjutAcara(Request $request)
    {
        // Validasi backend sesuai permintaan (opsional, karena JS sudah menangani)
        $request->validate([
            'metode_pengambilan' => 'required|in:ambil_sendiri,diantar',
            'tanggal_acara' => 'required|date',
        ]);

        if ($request->metode_pengambilan === 'diantar') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        }

        // KARENA ALUR PEMESANAN DITUTUP, KITA HANYA RETURN SUCCESS / REDIRECT DUMMY
        return back()->with('success', 'Data (' . $request->metode_pengambilan . ', ' . $request->tanggal_acara . ') berhasil dikirim ke proses berikutnya! (Placeholder)');
    }


        public function simpanLokasi(Request $request)
    {
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        if (!$lat || !$lng) {
            return back()->with('error', 'Silakan klik pada peta terlebih dahulu!');
        }

        // 1. Kenali siapa pelanggan yang sedang Login saat ini
        $user = auth()->user();

        // 2. Masukkan koordinat baru ke dalam tabel pelanggan tersebut
        $user->latitude = $lat;
        $user->longitude = $lng;
        
        // 3. Kunci dan simpan permanen ke Database MySQL
        $user->save();

        // 4. Kembalikan ke halaman peta dengan pesan sukses hijau
        return back()->with('success', 'Lokasi pengiriman berhasil disimpan secara permanen!');
    }

}
