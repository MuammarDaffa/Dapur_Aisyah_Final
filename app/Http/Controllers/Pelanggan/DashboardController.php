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
        $draft = session('pesanan_sementara', []);
        return view('pelanggan.lokasi_acara', compact('service', 'draft'));
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

        $draft = session('pesanan_sementara', []);
        $draft['layanan_id'] = $request->layanan_id;
        $draft['metode_pengambilan'] = $request->metode_pengambilan;
        $draft['tanggal_acara'] = $request->tanggal_acara;
        $draft['latitude'] = $request->latitude;
        $draft['longitude'] = $request->longitude;

        session(['pesanan_sementara' => $draft]);

        return redirect()->route('pelanggan.acara.pilih_menu')
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
    public function pilihMenuAcara(Request $request)
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['layanan_id'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan hilang, silakan mulai kembali.');
        }

        $layanan = \App\Models\Layanan::findOrFail($draft['layanan_id']);
        $menus = $layanan->menus()->with('items')->get();

        return view('pelanggan.pilih_menu_acara', compact('layanan', 'menus', 'draft'));
    }

    /**
     * Simpan Pilihan Menu Acara
     */
    public function simpanMenuAcara(Request $request)
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['layanan_id'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan hilang, silakan mulai kembali.');
        }

        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'tipe_penyajian' => 'required|in:Nasi Kotak,Prasmanan',
        ]);

        $menuId = $request->menu_id;
        $porsi = $request->input('porsi_' . $menuId);
        $items = $request->input('items_' . $menuId, []);

        if (!$porsi || $porsi < 50) {
            return back()->withErrors(['Jumlah porsi untuk menu yang dipilih minimal 50 porsi.']);
        }

        $menu = \App\Models\Menu::findOrFail($menuId);
        
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

        $hargaPerPorsi = $menu->harga + $subtotalItems;
        $totalHarga = $hargaPerPorsi * $porsi;

        $draft['menu_id'] = $menu->id;
        $draft['porsi'] = $porsi;
        $draft['item_menu'] = $selectedItems;
        $draft['subtotal'] = $totalHarga;
        $draft['total'] = $totalHarga;
        $draft['tipe_penyajian'] = $request->tipe_penyajian;

        session(['pesanan_sementara' => $draft]);

        return redirect()->route('pelanggan.acara.detail_pesanan')
                         ->with('success', 'Menu berhasil dipilih! Silakan periksa detail pesanan Anda.');
    }

    public function detailPesanan(Request $request)
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['layanan_id']) || !isset($draft['menu_id'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan tidak lengkap, silakan mulai kembali.');
        }

        $layanan = \App\Models\Layanan::find($draft['layanan_id']);
        $menu = \App\Models\Menu::find($draft['menu_id']);

        return view('pelanggan.detail_pesanan', compact('draft', 'layanan', 'menu'));
    }

    public function prosesBayar(Request $request)
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['layanan_id']) || !isset($draft['menu_id'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan tidak lengkap, silakan mulai kembali.');
        }

        $pesanan = \App\Models\Pesanan::create([
            'nomor_pesanan' => \App\Models\Pesanan::generateOrderNumber(),
            'user_id' => auth()->id(),
            'layanan_id' => $draft['layanan_id'],
            'menu_id' => $draft['menu_id'],
            'tanggal_pesanan' => $draft['tanggal_acara'],
            'metode_pengambilan' => $draft['metode_pengambilan'],
            'latitude' => $draft['latitude'],
            'longitude' => $draft['longitude'],
            'porsi' => $draft['porsi'],
            'item_menu' => $draft['item_menu'],
            'subtotal' => $draft['subtotal'],
            'total' => $draft['total'],
            'tipe_penyajian' => $draft['tipe_penyajian'],
            'status' => \App\Models\Pesanan::STATUS_BELUM_BAYAR,
        ]);

        session()->forget('pesanan_sementara');

        return redirect()->route('landing')
                         ->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan ke pembayaran.');
    }

}
