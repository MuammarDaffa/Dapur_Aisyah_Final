<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

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
            'tipe_penyajian' => 'required|in:Nasi Kotak,Prasmanan',
        ]);

        $menusDipilih = [];
        $totalHargaKeseluruhan = 0;

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'porsi_') && $value >= 50) {
                $menuId = str_replace('porsi_', '', $key);
                $porsi = (int) $value;
                $items = $request->input('items_' . $menuId, []);

                $menu = \App\Models\Menu::find($menuId);
                if ($menu) {
                    $subtotalItems = 0;
                    if (!empty($items)) {
                        $menuItems = \App\Models\MenuItem::whereIn('id', $items)->get();
                        foreach ($menuItems as $item) {
                            $subtotalItems += $item->harga;
                        }
                    }

                    $hargaPerPorsi = $menu->harga + $subtotalItems;
                    $subtotal = $hargaPerPorsi * $porsi;
                    $totalHargaKeseluruhan += $subtotal;

                    $menusDipilih[] = [
                        'menu_id' => $menu->id,
                        'porsi' => $porsi,
                        'menu_item_ids' => $items,
                        'subtotal' => $subtotal
                    ];
                }
            }
        }

        if (empty($menusDipilih)) {
            return back()->withErrors(['Silakan pilih minimal satu menu dengan porsi minimal 50 porsi.']);
        }

        $draft['menus'] = $menusDipilih;
        $draft['total'] = $totalHargaKeseluruhan;
        $draft['tipe_penyajian'] = $request->tipe_penyajian;

        session(['pesanan_sementara' => $draft]);

        return redirect()->route('pelanggan.acara.detail_pesanan')
                         ->with('success', 'Menu berhasil dipilih! Silakan periksa detail pesanan Anda.');
    }

    public function detailPesanan()
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['menus'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan hilang.');
        }

        $layanan = \App\Models\Layanan::find($draft['layanan_id']);

        $menusDetail = [];
        foreach ($draft['menus'] as $menuDraft) {
            $menu = \App\Models\Menu::find($menuDraft['menu_id']);
            $selectedItems = collect();
            if (!empty($menuDraft['menu_item_ids'])) {
                $selectedItems = \App\Models\MenuItem::whereIn('id', $menuDraft['menu_item_ids'])->get();
            }
            $menusDetail[] = [
                'menu' => $menu,
                'porsi' => $menuDraft['porsi'],
                'subtotal' => $menuDraft['subtotal'],
                'selectedItems' => $selectedItems
            ];
        }

        return view('pelanggan.detail_pesanan', compact('draft', 'layanan', 'menusDetail'));
    }

        public function prosesBayar(Request $request)
    {
        $draft = session('pesanan_sementara');
        if (!$draft || !isset($draft['layanan_id']) || !isset($draft['menus'])) {
            return redirect()->route('landing')->with('error', 'Sesi pesanan tidak lengkap, silakan mulai kembali.');
        }

        // 1. Asumsi DP 50% dari total harga
        $totalHarga = $draft['total'];
        $jumlahDp = $totalHarga * 0.5;
        $sisaPembayaran = $totalHarga - $jumlahDp;

        // 2. Simpan Data Pesanan ke Database
        $pesanan = \App\Models\Pesanan::create([
            'nomor_pesanan' => \App\Models\Pesanan::generateOrderNumber(),
            'user_id' => auth()->id(),
            'layanan_id' => $draft['layanan_id'],
            'tanggal_pesanan' => $draft['tanggal_acara'],
            'metode_pengambilan' => $draft['metode_pengambilan'],
            'latitude' => $draft['latitude'],
            'longitude' => $draft['longitude'],
            'subtotal' => $totalHarga, 
            'total' => $totalHarga,                     // <- Ini sudah saya perbaiki
            'jumlah_dp' => $jumlahDp,                   
            'sisa_pembayaran' => $sisaPembayaran,       
             'tipe_penyajian' => $draft['tipe_penyajian'] === 'Nasi Kotak' ? 'nasi_kotak' : 'prasmanan',
            'status' => \App\Models\Pesanan::STATUS_BELUM_BAYAR,
        ]);

        // 3. Simpan Detail Menu (Keranjang)
        foreach ($draft['menus'] as $menuDraft) {
            $detail = $pesanan->detailPesanans()->create([
                'menu_id' => $menuDraft['menu_id'],
                'porsi' => $menuDraft['porsi'],
                'subtotal' => $menuDraft['subtotal'],
            ]);

            if (!empty($menuDraft['menu_item_ids'])) {
                $detail->menuItems()->attach($menuDraft['menu_item_ids']);
            }
        }

        // 4. Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 5. Siapkan parameter pesanan DP ke Midtrans
        $params = array(
            'transaction_details' => array(
                'order_id' => $pesanan->nomor_pesanan . '-DP', 
                'gross_amount' => $jumlahDp,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );

        // 6. Dapatkan Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // 7. Hapus keranjang setelah token didapat
        session()->forget('pesanan_sementara');

            // 8. Karena kita menggunakan AJAX, kita kembalikan token-nya saja berupa JSON
     return response()->json([
         'status' => 'success',
         'snap_token' => $snapToken,
         'pesanan_id' => $pesanan->nomor_pesanan
     ]);

    }

    public function riwayatPesanan()
    {
        // 1. Ambil data pesanan milik pelanggan yang sedang login (user_id = auth()->id())
        // 2. Urutkan dari yang terbaru (latest)
        // 3. Ambil datanya (get)
        $riwayatPesanan = \App\Models\Pesanan::where('user_id', auth()->id())
                                             ->latest()
                                             ->get();

        // 4. Arahkan ke halaman riwayat dan bawa data tersebut
        return view('pelanggan.riwayat_pesanan', compact('riwayatPesanan'));
    }

        public function prosesPelunasan($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);
        
        // Keamanan: Pastikan pesanan ini benar milik pelanggan yang sedang login & statusnya DP
        if ($pesanan->user_id !== auth()->id() || $pesanan->status !== \App\Models\Pesanan::STATUS_DP) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Pesanan tidak valid untuk dilunasi'
            ], 403);
        }

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Siapkan parameter Midtrans khusus untuk PELUNASAN
        $params = array(
            'transaction_details' => array(
                // PERHATIKAN: Kita menambahkan akhiran -PELUNASAN di sini
                'order_id' => $pesanan->nomor_pesanan . '-PELUNASAN', 
                // Gross amount-nya menggunakan kolom sisa_pembayaran
                'gross_amount' => $pesanan->sisa_pembayaran,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Kembalikan token ini ke halaman Riwayat Pesanan
        return response()->json([
            'status' => 'success',
            'snap_token' => $snapToken
        ]);
    }


}
