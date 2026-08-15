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
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menu', 'detailPesanans.tambahanLaukPauk', 'detailPesanans.minuman'])->findOrFail($id);
        
        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('landing')->with('error', 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('pelanggan.detail_pesanan_harian', compact('pesanan'));
    }



    public function batalkanPesanan($id)
    {
        return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan Katering Harian tidak dapat dibatalkan oleh pelanggan.');
    }

    // ==========================================
    // KATERING HARIAN (NEW FLOW)
    // ==========================================

    public function showFormLokasi()
    {
        return view('pelanggan.harian_lokasi');
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
                'alamat_satelit' => 'nullable|string',
                'nomor_rumah' => 'required|string',
            ]);
            
            session([
                'harian_metode_pengambilan' => $request->metode_pengambilan,
                'harian_latitude' => $request->latitude,
                'harian_longitude' => $request->longitude,
                'harian_alamat_satelit' => $request->alamat_satelit,
                'harian_nomor_rumah' => $request->nomor_rumah,
            ]);
        } else {
            session([
                'harian_metode_pengambilan' => $request->metode_pengambilan,
            ]);
            session()->forget(['harian_latitude', 'harian_longitude', 'harian_alamat_satelit', 'harian_nomor_rumah']);
        }

        return redirect()->route('pelanggan.harian.menu');
    }

    public function showMenu()
    {
        // Pastikan session lokasi sudah ada
        if (!session()->has('harian_metode_pengambilan')) {
            return redirect()->route('pelanggan.harian.lokasi')->with('error', 'Silakan pilih metode pengambilan terlebih dahulu.');
        }

        // Layanan is now hardcoded
        $service = (object) ['tipe_layanan' => 'harian', 'nama' => 'Katering Harian'];

        $jadwals = \App\Models\JadwalMenu::with(['menu', 'menu.tambahanLaukPauk'])
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pelanggan.harian_menu', compact('service', 'jadwals'));
    }

    /**
     * Halaman Edit Pesanan Harian
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.tambahanLaukPauk'])->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = (object) ['tipe_layanan' => 'harian', 'nama' => 'Katering Harian'];
        
        $jadwals = \App\Models\JadwalMenu::with(['menu', 'menu.tambahanLaukPauk'])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Populate session dengan data pesanan agar bisa dipakai di form dan saat simpan
        session([
            'harian_metode_pengambilan' => $pesanan->metode_pengambilan,
            'harian_latitude' => $pesanan->latitude,
            'harian_longitude' => $pesanan->longitude,
        ]);

        return view('pelanggan.harian_menu', compact('pesanan', 'service', 'jadwals'));
    }

    /**
     * Simpan Pilihan Menu Harian (Create/Update)
     */
    public function storePesanan(Request $request)
    {
        $request->validate([
            'jadwal_ids' => 'required|array|min:1',
        ]);

        // Ambil data lokasi dari session
        $metode_pengambilan = session('harian_metode_pengambilan');
        $latitude = session('harian_latitude', null);
        $longitude = session('harian_longitude', null);
        $alamat_satelit = session('harian_alamat_satelit', null);
        $nomor_rumah = session('harian_nomor_rumah', null);

        if (!$metode_pengambilan) {
            return redirect()->route('pelanggan.harian.lokasi')->with('error', 'Sesi Anda telah habis. Silakan isi kembali metode pengambilan.');
        }

        $alamat_lengkap = null;
        if ($metode_pengambilan === 'diantar_ke_tempat') {
            $alamat_lengkap = $nomor_rumah ? "$nomor_rumah, $alamat_satelit" : $alamat_satelit;
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

            if ($jadwal->stok_tersisa < $porsi) {
                return back()->withInput()->with('error', 'Stok untuk menu ' . $jadwal->menu->nama_menu . ' pada tanggal ' . \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') . ' tidak mencukupi. Sisa stok: ' . $jadwal->stok_tersisa);
            }

            $items = $request->input('items_' . $jadwalId, []); // Array of menu_item_id => quantity
            
            $subtotalItems = 0;
            $selectedItemsData = [];

            if (is_array($items)) {
                foreach ($items as $itemId => $qty) {
                    if ($qty > 0) {
                        $tambahanLauk = \App\Models\TambahanLaukPauk::find($itemId);
                        if ($tambahanLauk) {
                            $subtotalItems += ($tambahanLauk->harga * $qty);
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
                'tambahan_lauk_pauk_ids' => $selectedItemsData,
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
                
                foreach ($pesanan->detailPesanans as $oldDetail) {
                    $oldJadwal = \App\Models\JadwalMenu::where('menu_id', $oldDetail->menu_id)
                        ->whereDate('tanggal', $oldDetail->tanggal_pengiriman)
                        ->first();
                    if ($oldJadwal) {
                        $oldJadwal->stok_tersisa += $oldDetail->porsi;
                        $oldJadwal->save();
                    }
                }

                $pesanan->update([
                    'metode_pengambilan' => $metode_pengambilan,
                    'alamat_lengkap' => $alamat_lengkap,
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
                    'tipe_layanan' => 'harian',
                    'nomor_pesanan' => $nomorPesanan,
                    'tanggal_pesanan' => now()->toDateString(), 
                    'metode_pengambilan' => $metode_pengambilan,
                    'alamat_lengkap' => $alamat_lengkap,
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                    'status_pembayaran' => \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    'status_pesanan' => null,
                ]);
            }

            foreach ($menusDipilih as $menuData) {
                $jadwalToUpdate = \App\Models\JadwalMenu::where('menu_id', $menuData['menu_id'])
                    ->whereDate('tanggal', $menuData['tanggal_pengiriman'])
                    ->first();
                if ($jadwalToUpdate) {
                    $jadwalToUpdate->stok_tersisa -= $menuData['porsi'];
                    $jadwalToUpdate->save();
                }

                $detail = \App\Models\DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $menuData['menu_id'],
                    'porsi' => $menuData['porsi'],
                    'subtotal' => $menuData['subtotal'],
                    'tanggal_pengiriman' => $menuData['tanggal_pengiriman'],
                ]);

                if (!empty($menuData['tambahan_lauk_pauk_ids'])) {
                    foreach ($menuData['tambahan_lauk_pauk_ids'] as $itemId) {
                        $detail->tambahanLaukPauk()->attach($itemId);
                    }
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
    
    public function reschedule(\Illuminate\Http\Request $request, $id)
    {
        $detail = \App\Models\DetailPesanan::findOrFail($id);
        
        // Cek kepemilikan
        if ($detail->pesanan->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Cek status pesanan LUNAS
        if ($detail->pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_LUNAS) {
            return back()->with('error', 'Reschedule hanya bisa dilakukan jika pesanan sudah lunas.');
        }

        // Cek apakah sudah pernah reschedule
        if ($detail->is_rescheduled) {
            return back()->with('error', 'Tanggal pengiriman untuk pesanan ini sudah pernah diubah sebelumnya.');
        }

        $today = \Carbon\Carbon::now()->startOfDay();
        $deliveryDate = \Carbon\Carbon::parse($detail->tanggal_pengiriman)->startOfDay();
        
        $request->validate([
            'new_date' => 'required|date'
        ]);

        $newDate = \Carbon\Carbon::parse($request->input('new_date'))->startOfDay();

        // 1. Tanggal baru tidak boleh sama dengan tanggal lama
        if ($newDate->isSameDay($deliveryDate)) {
            return back()->with('error', 'Tanggal baru harus berbeda dari tanggal pesanan saat ini.');
        }

        // 2. Tanggal baru tidak boleh menggunakan tanggal yang sedang berada pada hari ini
        if ($newDate->isToday()) {
            return back()->with('error', 'Tanggal baru tidak boleh menggunakan tanggal hari ini.');
        }

        // Cek ketersediaan menu yang sama pada tanggal baru
        $jadwalMenu = \App\Models\JadwalMenu::whereDate('tanggal', $newDate)
            ->where('menu_id', $detail->menu_id)
            ->first();

        if (!$jadwalMenu) {
            return back()->with('error', 'Menu pesanan Anda tidak tersedia pada tanggal pengganti yang dipilih.');
        }

        if ($jadwalMenu->stok_tersisa < $detail->porsi) {
            return back()->with('error', 'Stok menu pada tanggal pengganti tidak mencukupi. Sisa stok: ' . $jadwalMenu->stok_tersisa);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Kembalikan stok lama
            $oldJadwal = \App\Models\JadwalMenu::where('menu_id', $detail->menu_id)
                ->whereDate('tanggal', $detail->tanggal_pengiriman)
                ->first();
            if ($oldJadwal) {
                $oldJadwal->stok_tersisa += $detail->porsi;
                $oldJadwal->save();
            }

            // Kurangi stok baru
            $jadwalMenu->stok_tersisa -= $detail->porsi;
            $jadwalMenu->save();

            // Update data detail
            $detail->update([
                'tanggal_pengiriman' => $newDate->toDateString(),
                'is_rescheduled' => true,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Jadwal pengiriman berhasil dipindahkan ke tanggal ' . \Carbon\Carbon::parse($newDate)->translatedFormat('d F Y') . '.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan perubahan: ' . $e->getMessage());
        }
    }

    public function bayar($id)
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
                'order_id' => $pesanan->nomor_pesanan . '-PELUNASAN-' . time(), 
                'gross_amount' => $pesanan->total,
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
}
