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
        $minumans = $service->minumans;
        return view('pelanggan.pilih_menu_acara', compact('service', 'menus', 'minumans'));
    }

    /**
     * Halaman Edit Pesanan
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menuItems', 'detailPesananMinumans'])->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = \App\Models\Layanan::findOrFail($pesanan->layanan_id);
        $menus = $service->menus()->with('items')->get();
        $minumans = $service->minumans;

        return view('pelanggan.pilih_menu_acara', compact('pesanan', 'service', 'menus', 'minumans'));
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
        $customErrors = [];

        // Kumpulkan semua ID menu yang ada di request (dari porsi_ atau items_)
        $menuIds = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'porsi_')) {
                $menuIds[str_replace('porsi_', '', $key)] = true;
            } elseif (str_starts_with($key, 'items_')) {
                $menuIds[str_replace('items_', '', $key)] = true;
            }
        }

        foreach (array_keys($menuIds) as $menuId) {
            $porsiInput = $request->input('porsi_' . $menuId);
            $items = $request->input('items_' . $menuId, []);

            $hasPorsi = !empty($porsiInput) && is_numeric($porsiInput) && (int)$porsiInput > 0;
            $hasItems = !empty($items) && count($items) > 0;

            if ($hasPorsi && !$hasItems) {
                $customErrors['items_' . $menuId] = "Pilih minimal satu item menu.";
            } elseif ($hasItems && !$hasPorsi) {
                $customErrors['porsi_' . $menuId] = "Jumlah porsi wajib diisi.";
            } elseif ($hasPorsi && $hasItems) {
                $porsi = (int) $porsiInput;
                if ($porsi < 50) {
                    $customErrors['porsi_' . $menuId] = "Minimal pemesanan 50 porsi.";
                    continue;
                }

                $menu = \App\Models\Menu::find($menuId);
                if ($menu) {
                    $subtotalItems = 0;
                    $menuItems = \App\Models\MenuItem::whereIn('id', $items)->get();
                    foreach ($menuItems as $item) {
                        $subtotalItems += $item->harga;
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

        // Kumpulkan Minuman yang dipilih
        $minumanDipilih = [];
        $minumanIds = $request->input('minuman_ids', []);
        $jumlahCup = $request->input('jumlah_cup_minuman');
        
        $hasMinuman = is_array($minumanIds) && count($minumanIds) > 0;
        $hasJumlahCup = !empty($jumlahCup) && is_numeric($jumlahCup) && (int)$jumlahCup > 0;

        if ($hasMinuman && !$hasJumlahCup) {
            $customErrors['jumlah_cup_minuman'] = "Jumlah cup wajib diisi.";
        } elseif ($hasJumlahCup && !$hasMinuman) {
            $customErrors['minuman_ids'] = "Pilih minimal satu minuman.";
        } elseif ($hasMinuman && $hasJumlahCup) {
            $jumlahCupInt = (int) $jumlahCup;
            $minumans = \App\Models\Minuman::whereIn('id', $minumanIds)->get();
            
            foreach ($minumans as $minuman) {
                $subtotalMinuman = $minuman->harga * $jumlahCupInt;
                $totalHargaKeseluruhan += $subtotalMinuman;

                $minumanDipilih[] = [
                    'minuman_id' => $minuman->id,
                    'jumlah' => $jumlahCupInt,
                    'subtotal' => $subtotalMinuman
                ];
            }
        }


        if (count($customErrors) > 0) {
            return back()->withInput()->withErrors($customErrors);
        }

        if (empty($menusDipilih)) {
            return back()->withInput()->with('error', 'Silakan pilih minimal satu menu.');
        }
        
        $totalPorsiBaru = collect($menusDipilih)->sum('porsi');
        
        // Validasi Kapasitas Porsi Mingguan
        $layanan = \App\Models\Layanan::find($request->layanan_id);
        if ($layanan && $layanan->isAcara() && $layanan->kapasitas_porsi_per_minggu > 0) {
            $tanggalAcara = \Carbon\Carbon::parse($request->tanggal_acara);
            
            $totalPorsiSudahDipesan = \App\Models\DetailPesanan::whereHas('pesanan', function($q) use ($layanan, $tanggalAcara) {
                $q->where('layanan_id', $layanan->id)
                  ->whereNotNull('status_pesanan')
                  ->where('status_pesanan', '!=', 'dibatalkan')
                  ->whereBetween('tanggal_pesanan', [
                      $tanggalAcara->copy()->startOfWeek()->format('Y-m-d'),
                      $tanggalAcara->copy()->endOfWeek()->format('Y-m-d')
                  ]);
            })->sum('porsi');

            if (($totalPorsiSudahDipesan + $totalPorsiBaru) > $layanan->kapasitas_porsi_per_minggu) {
                return back()->withInput()->with('error', 'Mohon maaf, kuota pesanan untuk minggu pada tanggal tersebut sudah penuh. Silakan pilih tanggal acara di minggu lain.');
            }
        }

        $jumlahDp = $totalHargaKeseluruhan * 0.5;
        $sisaPembayaran = $totalHargaKeseluruhan - $jumlahDp;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($request->has('pesanan_id') && !empty($request->pesanan_id)) {
                $pesanan = \App\Models\Pesanan::findOrFail($request->pesanan_id);
                
                if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
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
                $pesanan->detailPesananMinumans()->delete();
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
                    'status_pesanan' => null,
                ]);
            }

            // Simpan detail pesanan baru (Makanan)
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

            // Simpan detail minuman
            if (!empty($minumanDipilih)) {
                foreach ($minumanDipilih as $minumanDraft) {
                    $pesanan->detailPesananMinumans()->create([
                        'minuman_id' => $minumanDraft['minuman_id'],
                        'jumlah' => $minumanDraft['jumlah'],
                        'subtotal' => $minumanDraft['subtotal'],
                    ]);
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
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menu', 'detailPesanans.menuItems', 'detailPesananMinumans.minuman', 'layanan'])->findOrFail($id);
        
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
                // PERHATIKAN: Kita menambahkan timestamp agar order_id selalu unik (menghindari error "order_id has already been taken")
                'order_id' => $pesanan->nomor_pesanan . '-DP-' . time(), 
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
                // PERHATIKAN: Kita menambahkan akhiran -PELUNASAN dan timestamp di sini
                'order_id' => $pesanan->nomor_pesanan . '-PELUNASAN-' . time(), 
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

        if ($pesanan->status_pesanan === \App\Models\Pesanan::PESANAN_DIBATALKAN) {
            return redirect()->route('pelanggan.riwayat')->with('info', 'Pesanan sudah berstatus dibatalkan.');
        }

        $pesanan->status_pesanan = \App\Models\Pesanan::PESANAN_DIBATALKAN;
        $pesanan->save();

        return redirect()->route('pelanggan.riwayat')->with('success', 'Pesanan berhasil dibatalkan.');
    }

}
