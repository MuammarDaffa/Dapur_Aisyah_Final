<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class KateringAcaraController extends Controller
{


    // ==========================================
    // KATERING ACARA KANTOR (NEW FLOW)
    // ==========================================

    public function showFormLokasi()
    {
        return view('pelanggan.acara_lokasi');
    }

    public function storeSessionLokasi(Request $request)
    {
        $request->validate([
            'tanggal_acara' => 'required|date|after_or_equal:' . \Carbon\Carbon::now()->addDays(5)->format('Y-m-d'),
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
                'acara_tanggal_acara' => $request->tanggal_acara,
                'acara_metode_pengambilan' => $request->metode_pengambilan,
                'acara_latitude' => $request->latitude,
                'acara_longitude' => $request->longitude,
                'acara_alamat_satelit' => $request->alamat_satelit,
                'acara_nomor_rumah' => $request->nomor_rumah,
            ]);
        } else {
            session([
                'acara_tanggal_acara' => $request->tanggal_acara,
                'acara_metode_pengambilan' => $request->metode_pengambilan,
            ]);
            session()->forget(['acara_latitude', 'acara_longitude', 'acara_alamat_satelit', 'acara_nomor_rumah']);
        }

        return redirect()->route('pelanggan.acara.menu');
    }

    public function showMenu()
    {
        // Pastikan session lokasi & tanggal sudah ada
        if (!session()->has('acara_tanggal_acara') || !session()->has('acara_metode_pengambilan')) {
            return redirect()->route('pelanggan.acara.lokasi')->with('error', 'Silakan isi tanggal acara dan metode pengambilan terlebih dahulu.');
        }

        $service = (object) ['tipe_layanan' => 'acara', 'nama' => 'Katering Acara'];
        $menus = \App\Models\Menu::where('tipe_layanan', 'acara')->get();
        $minumans = \App\Models\Minuman::where('tipe_layanan', 'acara')->get();
        
        return view('pelanggan.acara_menu', compact('service', 'menus', 'minumans'));
    }

    /**
     * Halaman Edit Pesanan
     */
    public function editPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans', 'detailPesanans.minuman'])->findOrFail($id);
        
        // Pastikan hanya bisa diedit jika belum_dibayar
        if ($pesanan->user_id !== auth()->id() || $pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return redirect()->route('pelanggan.riwayat')->with('error', 'Pesanan tidak valid atau sudah dibayar.');
        }

        $service = (object) ['tipe_layanan' => 'acara', 'nama' => 'Katering Acara'];
        $menus = \App\Models\Menu::where('tipe_layanan', 'acara')->get();
        $minumans = \App\Models\Minuman::where('tipe_layanan', 'acara')->get();

        // Populate session dengan data pesanan agar bisa dipakai di form dan saat simpan
        session([
            'acara_tanggal_acara' => \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('Y-m-d'),
            'acara_metode_pengambilan' => $pesanan->metode_pengambilan,
            'acara_latitude' => $pesanan->latitude,
            'acara_longitude' => $pesanan->longitude,
        ]);

        return view('pelanggan.acara_menu', compact('pesanan', 'service', 'menus', 'minumans'));
    }

    /**
     * Simpan Pilihan Menu Acara (Create / Update)
     */
    public function storePesanan(Request $request)
    {
        // layanan_id validation removed

        $tanggalAcara = session('acara_tanggal_acara');
        $metode_pengambilan = session('acara_metode_pengambilan');
        $latitude = session('acara_latitude', null);
        $longitude = session('acara_longitude', null);
        $alamat_satelit = session('acara_alamat_satelit', null);
        $nomor_rumah = session('acara_nomor_rumah', null);

        if (!$tanggalAcara || !$metode_pengambilan) {
            return redirect()->route('pelanggan.acara.lokasi')->with('error', 'Sesi Anda telah habis. Silakan isi kembali tanggal acara dan metode pengambilan.');
        }

        $alamat_lengkap = null;
        if ($metode_pengambilan === 'diantar_ke_tempat') {
            $alamat_lengkap = $nomor_rumah ? "$nomor_rumah, $alamat_satelit" : $alamat_satelit;
        }

        $menusDipilih = [];
        $totalHargaKeseluruhan = 0;
        $customErrors = [];

        // Kumpulkan semua ID menu yang ada di request
        $menuIds = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'porsi_') && !empty($value)) {
                $menuIds[str_replace('porsi_', '', $key)] = true;
            }
        }

        foreach (array_keys($menuIds) as $menuId) {
            $porsiInput = $request->input('porsi_' . $menuId);
            $tipePenyajianInput = $request->input('tipe_penyajian_' . $menuId);
            
            $hasPorsi = !empty($porsiInput) && is_numeric($porsiInput) && (int)$porsiInput > 0;

            if ($hasPorsi) {
                $porsi = (int) $porsiInput;
                if ($porsi < 50) {
                    $customErrors['porsi_' . $menuId] = "Minimal pemesanan 50 porsi.";
                    continue;
                }

                $menu = \App\Models\Menu::find($menuId);
                if ($menu) {
                    if ($menu->kategori_penyajian === 'bisa_pilih' && empty($tipePenyajianInput)) {
                        $customErrors['tipe_penyajian_' . $menuId] = "Pilih jenis penyajian.";
                        continue;
                    }

                    $tipePenyajian = $menu->kategori_penyajian === 'bisa_pilih' ? $tipePenyajianInput : 'Prasmanan';

                    $hargaPerPorsi = $menu->harga;
                    $subtotal = $hargaPerPorsi * $porsi;
                    $totalHargaKeseluruhan += $subtotal;

                    $menusDipilih[] = [
                        'menu_id' => $menu->id,
                        'porsi' => $porsi,
                        'tipe_penyajian' => $tipePenyajian,
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
            return back()->withInput()->with('error', 'Silakan isi jumlah porsi minimal pada satu menu.');
        }
        
        $totalPorsiBaru = collect($menusDipilih)->sum('porsi');
        
        // Cek Kuota Porsi per Minggu (Maks 200) khusus Katering Acara
        $tanggalAcaraObj = \Carbon\Carbon::parse($tanggalAcara);
        $startOfWeek = $tanggalAcaraObj->copy()->startOfWeek();
        $endOfWeek = $tanggalAcaraObj->copy()->endOfWeek();

        $pesananQuery = \App\Models\Pesanan::where('tipe_layanan', 'acara')
            ->whereBetween('tanggal_pesanan', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where(function ($q) {
                $q->whereNull('status_pesanan')
                  ->orWhere('status_pesanan', '!=', \App\Models\Pesanan::PESANAN_DIBATALKAN);
            });

        if ($request->has('pesanan_id') && !empty($request->pesanan_id)) {
            $pesananQuery->where('id', '!=', $request->pesanan_id);
        }

        $pesananTerkonfirmasi = $pesananQuery->get();

        $porsiTelahDipesan = 0;
        foreach ($pesananTerkonfirmasi as $p) {
            $porsiTelahDipesan += $p->detailPesanans->whereNotNull('menu_id')->sum('porsi');
        }

        if (($porsiTelahDipesan + $totalPorsiBaru) > 200) {
            $sisaKuota = max(0, 200 - $porsiTelahDipesan);
            $pesanError = 'Maaf, sisa kuota Katering Acara untuk minggu tersebut (' . $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y') . ') tidak mencukupi. Sisa kuota minggu itu: ' . $sisaKuota . ' porsi.';
            return back()->withInput()->with('error', $pesanError);
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
                    'tanggal_pesanan' => $tanggalAcara,
                    'event_start_time' => $tanggalAcara . ' 08:00:00', // Default jam jika diperlukan
                    'metode_pengambilan' => $metode_pengambilan,
                    'alamat_lengkap' => $alamat_lengkap,
                    'tipe_penyajian' => null, // removed global tipe_penyajian
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                ]);

                // Hapus detail lama untuk diganti yang baru
                $pesanan->detailPesanans()->delete();
            } else {
                $pesanan = \App\Models\Pesanan::create([
                    'user_id' => auth()->id(),
                    'tipe_layanan' => 'acara',
                    'nomor_pesanan' => \App\Models\Pesanan::generateOrderNumber(),
                    'tanggal_pesanan' => $tanggalAcara,
                    'event_start_time' => $tanggalAcara . ' 08:00:00',
                    'metode_pengambilan' => $metode_pengambilan,
                    'alamat_lengkap' => $alamat_lengkap,
                    'subtotal' => $totalHargaKeseluruhan,
                    'total' => $totalHargaKeseluruhan,
                    'jumlah_dp' => $jumlahDp,
                    'sisa_pembayaran' => $sisaPembayaran,
                    'tipe_penyajian' => null, // removed global tipe_penyajian
                    'status_pembayaran' => \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR,
                    'status_pesanan' => null,
                ]);
            }

            // Simpan detail pesanan baru (Makanan)
            foreach ($menusDipilih as $menuDraft) {
                $pesanan->detailPesanans()->create([
                    'menu_id' => $menuDraft['menu_id'],
                    'porsi' => $menuDraft['porsi'],
                    'tipe_penyajian' => $menuDraft['tipe_penyajian'],
                    'subtotal' => $menuDraft['subtotal'],
                ]);
            }

            // Simpan detail minuman
            if (!empty($minumanDipilih)) {
                foreach ($minumanDipilih as $minumanDraft) {
                    $pesanan->detailPesanans()->create([
                        'minuman_id' => $minumanDraft['minuman_id'],
                        'porsi' => $minumanDraft['jumlah'],
                        'subtotal' => $minumanDraft['subtotal'],
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            session()->forget(['pesanan_sementara', 'acara_tanggal_acara', 'acara_metode_pengambilan', 'acara_latitude', 'acara_longitude', 'acara_alamat_satelit', 'acara_nomor_rumah']);

            return redirect()->route('pelanggan.acara.detail_pesanan', $pesanan->id);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pesanan: ' . $e->getMessage());
        }
    }

    public function detailPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with(['detailPesanans.menu', 'detailPesanans.minuman'])->findOrFail($id);
        
        if ($pesanan->user_id !== auth()->id()) {
            return redirect()->route('landing')->with('error', 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('pelanggan.detail_pesanan_acara', compact('pesanan'));
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

        $tipePembayaran = ($pesanan->jumlah_dp == $pesanan->total) ? 'PELUNASAN' : 'DP';

        $params = array(
            'transaction_details' => array(
                // PERHATIKAN: Kita menambahkan timestamp agar order_id selalu unik (menghindari error "order_id has already been taken")
                'order_id' => $pesanan->nomor_pesanan . '-' . $tipePembayaran . '-' . time(), 
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
