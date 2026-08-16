<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Layanan removed
use Illuminate\Http\Request;

class CateringAcaraController extends Controller
{
    // =======================================
    // File : app/Http/Controllers/Admin/CateringAcaraController.php
    // Fungsi : Menampilkan halaman detail spesifik dari satu katering acara.
    // Dijalankan Kapan : Ketika admin menekan tombol ikon mata (Detail) di halaman daftar katering (Tipe Acara).
    // Data berasal dari mana : Model Layanan berdasarkan ID yang diklik.
    // Data dikirim ke mana : Halaman resources/views/admin/catering/show.blade.php
    // =======================================
    public function index(Request $request, string $tipe_layanan = 'acara')
    {
        // Pastikan katering bertipe acara
        if ($tipe_layanan !== 'acara') {
            return redirect()->route('admin.dashboard')->with('error', 'Layanan ini bukan tipe Acara.');
        }

        // --- Logika Stok Porsi Mingguan ---
        $tanggalTerpilih = $request->query('tanggal', now()->toDateString());
        $tanggalObj = \Carbon\Carbon::parse($tanggalTerpilih);
        $startOfWeek = $tanggalObj->copy()->startOfWeek();
        $endOfWeek = $tanggalObj->copy()->endOfWeek();

        $stokMingguan = \App\Models\StokPorsiAcara::where('tanggal_mulai', $startOfWeek->toDateString())
                            ->where('tanggal_selesai', $endOfWeek->toDateString())
                            ->first();

        $stok = $stokMingguan ? $stokMingguan->stok : 0;

        $pesananQuery = \App\Models\Pesanan::with('detailPesanans')->where('tipe_layanan', 'acara')
            ->whereBetween('tanggal_pesanan', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('status_pesanan', '!=', \App\Models\Pesanan::PESANAN_DIBATALKAN)
            ->whereIn('status_pembayaran', [\App\Models\Pesanan::PEMBAYARAN_DP, \App\Models\Pesanan::PEMBAYARAN_LUNAS]);

        $pesananTerkonfirmasi = $pesananQuery->get();
        
        $terjual = 0;
        foreach ($pesananTerkonfirmasi as $p) {
            $terjual += $p->detailPesanans->whereNotNull('menu_id')->sum('porsi');
        }

        $sisaStok = max(0, $stok - $terjual);
        
        $periodeMinggu = [
            'start' => $startOfWeek->toDateString(),
            'end' => $endOfWeek->toDateString(),
            'start_formatted' => $startOfWeek->translatedFormat('d M Y'),
            'end_formatted' => $endOfWeek->translatedFormat('d M Y'),
            'tanggal_terpilih' => $tanggalTerpilih
        ];
        // --- End Logika ---

        $menus = \App\Models\Menu::with('tambahanLaukPauk')->where('tipe_layanan', 'acara')->get();
        $minumans = \App\Models\Minuman::where('tipe_layanan', 'acara')->get();
        
        return view('admin.catering.show', compact(
            'menus',
            'minumans',
            'stok',
            'terjual',
            'sisaStok',
            'periodeMinggu'
        ));
    }

    public function storeStok(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'stok' => 'required|integer|min:0',
            'terjual' => 'required|integer|min:0',
        ]);

        if ($request->stok < $request->terjual) {
            return back()->with('swal_error', 'Stok per minggu tidak boleh lebih kecil dari jumlah porsi yang sudah terjual.');
        }

        \App\Models\StokPorsiAcara::updateOrCreate(
            [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ],
            [
                'stok' => $request->stok,
            ]
        );

        return back()->with('swal_success', 'Pengaturan porsi mingguan berhasil disimpan.');
    }
}
