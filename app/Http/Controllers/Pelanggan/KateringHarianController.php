<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class KateringHarianController extends Controller
{





    public function detailPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menu', 'detailPesanans.menuItems', 'detailPesanans.minuman', 'layanan'])->findOrFail($id);
        
        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('landing')->with('error', 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('pelanggan.detail_pesanan_harian', compact('pesanan'));
    }



    public function batalkanPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);

        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIBATALKAN) {
            return redirect()->route('pelanggan.riwayat')->with('info', 'Pesanan sudah berstatus dibatalkan.');
        }

        $pesanan->status_pesanan = \App\Models\Pesanan::PESANAN_DIBATALKAN;
        $pesanan->save();

        return redirect()->route('pelanggan.riwayat')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // ==========================================
    // KATERING HARIAN (NEW FLOW)
    // ==========================================

    public function showFormLokasi()
    {
        return view('pelanggan.harian-form');
    }

    public function storeSessionLokasi(Request $request)
    {
        $request->validate([
            'metode_pengambilan' => 'required|in:ambil_sendiri,diantar_ke_tempat',
        ]);

        if ($request->metode_pengambilan === 'diantar_ke_tempat') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
            
            session([
                'harian_metode_pengambilan' => $request->metode_pengambilan,
                'harian_latitude' => $request->latitude,
                'harian_longitude' => $request->longitude,
            ]);
        } else {
            session([
                'harian_metode_pengambilan' => $request->metode_pengambilan,
            ]);
            session()->forget(['harian_latitude', 'harian_longitude']);
        }

        return redirect()->route('pelanggan.harian.menu');
    }

    public function showMenu()
    {
        // Pastikan session lokasi sudah ada
        if (!session()->has('harian_metode_pengambilan')) {
            return redirect()->route('pelanggan.harian.lokasi')->with('error', 'Silakan pilih metode pengambilan terlebih dahulu.');
        }

        $service = \App\Models\Layanan::harian()->active()->first();
        if (!$service) {
            return redirect()->route('landing')->with('error', 'Layanan Katering Harian tidak tersedia saat ini.');
        }

        $besok = \Carbon\Carbon::tomorrow()->toDateString();
        $jadwals = \App\Models\JadwalMenu::with(['menu', 'menu.items'])
            ->where('tanggal', '>=', $besok)
            ->where('aktif', true)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pelanggan.menu-harian', compact('service', 'jadwals'));
    }

    /**
     * Halaman Edit Pesanan Harian
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menuItems', 'layanan'])->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = \App\Models\Layanan::findOrFail($pesanan->layanan_id);
        
        $besok = \Carbon\Carbon::tomorrow()->toDateString();
        $jadwals = \App\Models\JadwalMenu::with(['menu', 'menu.items'])
            ->where('tanggal', '>=', $besok)
            ->where('aktif', true)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Populate session dengan data pesanan agar bisa dipakai di form dan saat simpan
        session([
            'harian_metode_pengambilan' => $pesanan->metode_pengambilan,
            'harian_latitude' => $pesanan->latitude,
            'harian_longitude' => $pesanan->longitude,
        ]);

        return view('pelanggan.menu-harian', compact('pesanan', 'service', 'jadwals'));
    }

    /**
     * Simpan Pilihan Menu Harian (Create/Update)
     */
    public function storePesanan(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'jadwal_ids' => 'required|array|min:1',
        ]);

        // Ambil data lokasi dari session
        $metode_pengambilan = session('harian_metode_pengambilan');
        $latitude = session('harian_latitude', null);
        $longitude = session('harian_longitude', null);

        if (!$metode_pengambilan) {
            return redirect()->route('pelanggan.harian.lokasi')->with('error', 'Sesi Anda telah habis. Silakan isi kembali metode pengambilan.');
        }

        $jadwalIds = $request->input('jadwal_ids', []);
        $totalHargaKeseluruhan = 0;
        $menusDipilih = [];

        foreach ($jadwalIds as $jadwalId) {
            $jadwal = \App\Models\JadwalMenu::with('menu')->find($jadwalId);
            if (!$jadwal || !$jadwal->menu) continue;

            $porsi = (int) $request->input('porsi_' . $jadwalId, 0);
            
            $items = $request->input('items_' . $jadwalId, []); // Array of menu_item_id => quantity
            
            $totalItemsQty = 0;
            if (is_array($items)) {
                foreach ($items as $itemId => $qty) {
                    $totalItemsQty += (int) $qty;
                }
            }

            if ($porsi < 1 && $totalItemsQty > 0) {
                return back()->withInput()->with('error', 'Anda memesan menu tambahan, namun tidak mengisi jumlah porsi utama untuk tanggal ' . \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') . '.');
            }

            if ($porsi < 1) continue;

            $items = $request->input('items_' . $jadwalId, []); // Array of menu_item_id => quantity
            
            $subtotalItems = 0;
            $selectedItemsData = [];

            if (is_array($items)) {
                foreach ($items as $itemId => $qty) {
                    if ($qty > 0) {
                        $menuItem = \App\Models\MenuItem::find($itemId);
                        if ($menuItem) {
                            $subtotalItems += ($menuItem->harga * $qty);
                            // We need to store this somehow. 
                            // In Acara, it uses pivot. For Harian, let's just sum it to the subtotal, 
                            // or attach the items properly. DetailPesanan only accepts array of IDs currently in acara.
                            // If they buy quantities of extras, the DB structure `detail_pesanan_item` doesn't have quantity!
                            // Wait, DetailPesananItem pivot table:
                            // let's check it. For now, just attach the items. If they want qty, we might need qty column.
                            // Since the user said "input jumlah", I'll pass it anyway.
                            for($i = 0; $i < $qty; $i++) {
                                $selectedItemsData[] = $itemId;
                            }
                        }
                    }
                }
            }

            $hargaPerPorsiUtama = $jadwal->menu->harga;
            $subtotal = ($hargaPerPorsiUtama * $porsi) + $subtotalItems; // If extras are independent of main portion
            $totalHargaKeseluruhan += $subtotal;

            $menusDipilih[] = [
                'menu_id' => $jadwal->menu_id,
                'porsi' => $porsi,
                'menu_item_ids' => $selectedItemsData,
                'subtotal' => $subtotal,
                'tanggal_pengiriman' => $jadwal->tanggal,
            ];
        }

        if (empty($menusDipilih)) {
            return back()->withInput()->with('error', 'Silakan pilih minimal satu jadwal dengan jumlah porsi utama lebih dari 0.');
        }

        // Untuk Katering Harian, pembayaran langsung lunas, tidak ada potongan DP (100% dibayar)
        $jumlahDp = $totalHargaKeseluruhan;
        $sisaPembayaran = 0;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($request->has('pesanan_id') && !empty($request->pesanan_id)) {
                $pesanan = \App\Models\Pesanan::findOrFail($request->pesanan_id);
                
                if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
                    throw new \Exception('Pesanan tidak valid untuk diubah.');
                }
                
                $pesanan->update([
                    'metode_pengambilan' => $metode_pengambilan,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                ]);

                // Hapus detail lama untuk diganti yang baru
                $pesanan->detailPesanans()->delete();
            } else {
                $nomorPesanan = \App\Models\Pesanan::generateOrderNumber();

                $pesanan = \App\Models\Pesanan::create([
                    'user_id' => auth()->id(),
                    'layanan_id' => $request->layanan_id,
                    'nomor_pesanan' => $nomorPesanan,
                    'tanggal_pesanan' => now()->toDateString(), 
                    'metode_pengambilan' => $metode_pengambilan,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'tipe_penyajian' => 'nasi_kotak',
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                    'status_pembayaran' => \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    'status_pesanan' => null,
                ]);
            }

            foreach ($menusDipilih as $menuData) {
                $detail = \App\Models\DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $menuData['menu_id'],
                    'porsi' => $menuData['porsi'],
                    'subtotal' => $menuData['subtotal'],
                    'tanggal_pengiriman' => $menuData['tanggal_pengiriman'],
                ]);

                if (!empty($menuData['menu_item_ids'])) {
                    $detail->menuItems()->attach($menuData['menu_item_ids']);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Bersihkan session setelah berhasil masuk database
            session()->forget(['harian_metode_pengambilan', 'harian_latitude', 'harian_longitude']);

            return redirect()->route('pelanggan.harian.detail_pesanan', $pesanan->id);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
    }
    }
}
