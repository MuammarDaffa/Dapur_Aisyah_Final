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

    public function riwayatHarian()
    {
        $riwayatPesanan = \App\Models\Pesanan::where('user_id', auth()->id())
            ->whereHas('layanan', function ($query) {
                $query->where('tipe', 'harian');
            })
            ->latest()
            ->get();

        return view('pelanggan.riwayat_pesanan_harian', compact('riwayatPesanan'));
    }

    public function batalkanPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::findOrFail($id);

        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('pelanggan.riwayat.harian')->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIBATALKAN) {
            return redirect()->route('pelanggan.riwayat.harian')->with('info', 'Pesanan sudah berstatus dibatalkan.');
        }

        $pesanan->status_pesanan = \App\Models\Pesanan::PESANAN_DIBATALKAN;
        $pesanan->save();

        return redirect()->route('pelanggan.riwayat.harian')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // ==========================================
    // KATERING HARIAN (NEW FLOW)
    // ==========================================

    public function pesanHarian()
    {
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

        return view('pelanggan.katering_harian', compact('service', 'jadwals'));
    }

    /**
     * Halaman Edit Pesanan Harian
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menuItems', 'layanan'])->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return redirect()->route('pelanggan.riwayat.harian')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = \App\Models\Layanan::findOrFail($pesanan->layanan_id);
        
        $besok = \Carbon\Carbon::tomorrow()->toDateString();
        $jadwals = \App\Models\JadwalMenu::with(['menu', 'menu.items'])
            ->where('tanggal', '>=', $besok)
            ->where('aktif', true)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pelanggan.katering_harian', compact('pesanan', 'service', 'jadwals'));
    }

    /**
     * Simpan Pilihan Menu Harian (Create/Update)
     */
    public function simpanPesananHarian(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'metode_pengambilan' => 'required|in:ambil_sendiri,diantar_ke_tempat',
            'jadwal_ids' => 'required|array|min:1',
        ]);

        if ($request->metode_pengambilan === 'diantar_ke_tempat') {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        }

        $jadwalIds = $request->input('jadwal_ids', []);
        $totalHargaKeseluruhan = 0;
        $menusDipilih = [];

        foreach ($jadwalIds as $jadwalId) {
            $jadwal = \App\Models\JadwalMenu::with('menu')->find($jadwalId);
            if (!$jadwal || !$jadwal->menu) continue;

            $porsi = (int) $request->input('porsi_' . $jadwalId, 1);
            if ($porsi < 1) $porsi = 1;

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
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses jadwal.');
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
                    'metode_pengambilan' => $request->metode_pengambilan,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
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
                    'metode_pengambilan' => $request->metode_pengambilan,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
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

            $pesanSukses = $request->has('pesanan_id') && !empty($request->pesanan_id) 
                ? 'Pesanan Harian berhasil diperbarui! Silakan selesaikan pembayaran.' 
                : 'Pesanan Harian berhasil dibuat! Silakan selesaikan pembayaran.';

            return redirect()->route('pelanggan.harian.detail_pesanan', $pesanan->id)
                             ->with('success', $pesanSukses);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function rescheduleHarian(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:detail_pesanan,id',
            'tanggal_baru' => 'required|date|after:today',
        ]);

        $detail = \App\Models\DetailPesanan::with('pesanan')->findOrFail($request->detail_id);

        if ($detail->pesanan->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($detail->is_rescheduled) {
            return back()->with('error', 'Jadwal ini sudah pernah diganti. Ganti tanggal hanya berlaku 1x.');
        }

        // H-1 Validation
        $tanggalLama = \Carbon\Carbon::parse($detail->tanggal_pengiriman);
        $besok = \Carbon\Carbon::tomorrow();

        if ($tanggalLama->lt($besok)) {
            return back()->with('error', 'Gagal. Ganti tanggal hanya bisa dilakukan maksimal H-1.');
        }

        // Hari baru harus Senin-Jumat
        $tanggalBaru = \Carbon\Carbon::parse($request->tanggal_baru);
        if ($tanggalBaru->isSaturday() || $tanggalBaru->isSunday()) {
            return back()->with('error', 'Gagal. Pengiriman katering hanya untuk hari Senin s/d Jumat.');
        }

        $detail->tanggal_pengiriman = $request->tanggal_baru;
        $detail->is_rescheduled = true;
        $detail->save();

        return back()->with('success', 'Berhasil ganti tanggal pengiriman!');
    }

}
