<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{


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
    public function acaraService(Request $request, \App\Models\Layanan $service)
    {
        $pesanan = null;
        if ($request->has('pesanan_id')) {
            $pesanan = \App\Models\Pesanan::find($request->pesanan_id);
        }
        return view('pelanggan.lokasi_acara', compact('service', 'pesanan'));
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
            'layanan_id' => 'required|exists:layanan,id',
            'metode_pengambilan' => 'required|in:ambil_sendiri,diantar_ke_tempat',
            'tanggal_acara' => 'required|date',
        ]);

        if ($request->metode_pengambilan === 'diantar_ke_tempat') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        }

        if ($request->has('pesanan_id') && $request->pesanan_id) {
            $pesanan = \App\Models\Pesanan::findOrFail($request->pesanan_id);
            $pesanan->update([
                'tanggal_pesanan' => $request->tanggal_acara,
                'metode_pengambilan' => $request->metode_pengambilan,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } else {
            // Simpan data pesanan baru
            $pesanan = \App\Models\Pesanan::create([
                'nomor_pesanan' => \App\Models\Pesanan::generateOrderNumber(),
                'user_id' => auth()->id(),
                'layanan_id' => $request->layanan_id,
                'tanggal_pesanan' => $request->tanggal_acara,
                'metode_pengambilan' => $request->metode_pengambilan,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'porsi' => 0,
                'subtotal' => 0,
                'total' => 0,
                'status' => \App\Models\Pesanan::STATUS_BELUM_BAYAR,
            ]);
        }

        return redirect()->route('pelanggan.acara.pilih_menu', $pesanan->id)
                         ->with('success', 'Lokasi dan tanggal berhasil disimpan. Silakan pilih menu Anda.');
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

        // 2. KARENA ALUR DITUTUP, HANYA PLACEHOLDER TAMPILAN
        // Koordinat latitude dan longitude tidak lagi disimpan ke tabel users.
        // Data ini seharusnya akan diteruskan ke proses checkout dan disimpan di tabel pesanan.
        
        // 3. Kembalikan ke halaman peta dengan pesan sukses hijau
        return back()->with('success', 'Lokasi pengiriman disetujui (Placeholder). Data siap diproses ke pesanan!');
    }

    /**
     * Halaman Pilih Menu Acara
     */
    public function pilihMenuAcara($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);
        
        // Pastikan pesanan ini milik user yang login dan belum dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status !== \App\Models\Pesanan::STATUS_BELUM_BAYAR) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $layanan = $pesanan->layanan;
        // Ambil semua menu yang terkait dengan layanan ini beserta items-nya
        $menus = $layanan->menus()->with('items')->get();

        return view('pelanggan.pilih_menu_acara', compact('pesanan', 'layanan', 'menus'));
    }

    /**
     * Simpan Pilihan Menu Acara
     */
    public function simpanMenuAcara(Request $request, $id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);
        
        // Pastikan pesanan ini milik user yang login
        if ($pesanan->user_id !== auth()->id() || $pesanan->status !== \App\Models\Pesanan::STATUS_BELUM_BAYAR) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $request->validate([
            'menu_id' => 'required|exists:menu,id',
        ]);

        $menuId = $request->menu_id;
        $porsi = $request->input('porsi_' . $menuId);
        $items = $request->input('items_' . $menuId, []);

        if (!$porsi || $porsi < 50) {
            return back()->withErrors(['Jumlah porsi untuk menu yang dipilih minimal 50 porsi.']);
        }

        $menu = \App\Models\Menu::findOrFail($menuId);
        
        // Persiapkan data items yang dipilih (untuk disimpan di JSON item_menu)
        $selectedItems = [];
        $subtotalItems = 0;
        
        if (!empty($items)) {
            $menuItems = \App\Models\MenuItem::whereIn('id', $items)->get();
            foreach ($menuItems as $item) {
                $selectedItems[] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'harga' => $item->harga,
                ];
                $subtotalItems += $item->harga;
            }
        }

        // Hitung total: (harga menu + harga items) * porsi
        $hargaPerPorsi = $menu->harga + $subtotalItems;
        $totalHarga = $hargaPerPorsi * $porsi;

        $pesanan->update([
            'menu_id' => $menu->id,
            'porsi' => $porsi,
            'item_menu' => $selectedItems,
            'subtotal' => $totalHarga,
            'total' => $totalHarga, // Total sama dengan subtotal (belum ada ongkir dll)
        ]);

        return redirect()->route('landing')
                         ->with('success', 'Pesanan menu berhasil disimpan! Silakan lanjutkan ke pembayaran.');
    }

}
