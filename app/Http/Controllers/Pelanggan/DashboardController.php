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
     */
    public function acaraService(Request $request, \App\Models\Layanan $service)
    {
        $menus = $service->menus()->with('items')->get();
        return view('pelanggan.pilih_menu_acara', compact('service', 'menus'));
    }

    /**
     * Halaman Edit Pesanan
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with('detailPesanans.menuItems')->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_bayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status !== \App\Models\Pesanan::STATUS_BELUM_BAYAR) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = \App\Models\Layanan::findOrFail($pesanan->layanan_id);
        $menus = $service->menus()->with('items')->get();

        return view('pelanggan.pilih_menu_acara', compact('pesanan', 'service', 'menus'));
    }

    /**
     * Simpan Pilihan Menu Acara (Create / Update)
     */
    public function simpanMenuAcara(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'metode_pengambilan' => 'required|in:ambil_sendiri,diantar_ke_tempat',
            'tanggal_acara' => 'required|date',
            'tipe_penyajian' => 'required|in:Nasi Kotak,Prasmanan',
        ]);

        if ($request->metode_pengambilan === 'diantar_ke_tempat') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        }

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
            return back()->withInput()->withErrors(['Silakan pilih minimal satu menu dengan porsi minimal 50 porsi.']);
        }

        $jumlahDp = $totalHargaKeseluruhan * 0.5;
        $sisaPembayaran = $totalHargaKeseluruhan - $jumlahDp;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($request->has('pesanan_id') && !empty($request->pesanan_id)) {
                $pesanan = \App\Models\Pesanan::findOrFail($request->pesanan_id);
                
                if ($pesanan->user_id !== auth()->id() || $pesanan->status !== \App\Models\Pesanan::STATUS_BELUM_BAYAR) {
                    throw new \Exception('Pesanan tidak valid untuk diubah.');
                }
                
                $pesanan->update([
                    'tanggal_pesanan' => $request->tanggal_acara,
                    'metode_pengambilan' => $request->metode_pengambilan,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'tipe_penyajian' => $request->tipe_penyajian === 'Nasi Kotak' ? 'nasi_kotak' : 'prasmanan',
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                ]);

                // Hapus detail lama untuk diganti yang baru
                $pesanan->detailPesanans()->delete();
            } else {
                $pesanan = \App\Models\Pesanan::create([
                    'nomor_pesanan' => \App\Models\Pesanan::generateOrderNumber(),
                    'user_id' => auth()->id(),
                    'layanan_id' => $request->layanan_id,
                    'tanggal_pesanan' => $request->tanggal_acara,
                    'metode_pengambilan' => $request->metode_pengambilan,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'subtotal' => $totalHargaKeseluruhan, 
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,                   
                    'sisa_pembayaran' => $sisaPembayaran,       
                    'tipe_penyajian' => $request->tipe_penyajian === 'Nasi Kotak' ? 'nasi_kotak' : 'prasmanan',
                    'status_pembayaran' => \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    'status_pesanan' => \App\Models\Pesanan::PESANAN_DIPROSES,
                ]);
            }

            // Simpan detail pesanan baru
            foreach ($menusDipilih as $menuDraft) {
                $detail = $pesanan->detailPesanans()->create([
                    'menu_id' => $menuDraft['menu_id'],
                    'porsi' => $menuDraft['porsi'],
                    'subtotal' => $menuDraft['subtotal'],
                ]);

                if (!empty($menuDraft['menu_item_ids'])) {
                    $detail->menuItems()->attach($menuDraft['menu_item_ids']);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            session()->forget('pesanan_sementara');

            return redirect()->route('pelanggan.acara.detail_pesanan', $pesanan->id)
                             ->with('success', 'Pesanan berhasil disimpan, silakan periksa detail pesanan Anda.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pesanan: ' . $e->getMessage());
        }
    }

    public function detailPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menu', 'detailPesanans.menuItems', 'layanan'])->findOrFail($id);
        
        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('landing')->with('error', 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('pelanggan.detail_pesanan', compact('pesanan'));
    }

    public function bayarDp($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);
        
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak valid untuk dibayar'], 403);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $pesanan->nomor_pesanan . '-DP', 
                'gross_amount' => $pesanan->jumlah_dp,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );

        $snapToken = Snap::getSnapToken($params);

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
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_DP) {
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

    public function batalkanPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);

        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($pesanan->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan yang sudah lunas tidak dapat dibatalkan.');
        }

        if ($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIBATALKAN) {
            return redirect()->route('pelanggan.riwayat')->with('info', 'Pesanan sudah berstatus dibatalkan.');
        }

        $pesanan->status_pesanan = \App\Models\Pesanan::PESANAN_DIBATALKAN;
        $pesanan->save();

        return redirect()->route('pelanggan.riwayat')->with('success', 'Pesanan berhasil dibatalkan.');
    }

}
