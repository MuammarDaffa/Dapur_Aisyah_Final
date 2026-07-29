<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
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
    public function index(Request $request, Layanan $layanan)
    {
        // Pastikan katering bertipe acara
        if (!$layanan->isAcara()) {
            return redirect()->route('admin.catering.index')->with('error', 'Layanan ini bukan tipe Acara.');
        }

        $kapasitas_porsi_per_minggu = $layanan->kapasitas_porsi_per_minggu ?? 0;
        $jumlah_porsi_terjual_minggu_ini = 0;
        $sisa_porsi_minggu_ini = $kapasitas_porsi_per_minggu;

        // Ambil tanggal filter dari request (default: hari ini)
        $tanggal_filter = $request->input('tanggal', now()->format('Y-m-d'));
        $startOfWeek = \Carbon\Carbon::parse($tanggal_filter)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($tanggal_filter)->endOfWeek()->format('Y-m-d');

        $layanan->load(['menus.items', 'minumans']);
        $menus = $layanan->menus;
        $minumans = $layanan->minumans;
        
        $jumlah_porsi_terjual_minggu_ini = (int) \App\Models\DetailPesanan::whereHas('pesanan', function($q) use ($layanan, $startOfWeek, $endOfWeek) {
            $q->where('layanan_id', $layanan->id)
                ->where('status_pesanan', '!=', 'dibatalkan')
                ->whereBetween('tanggal_pesanan', [$startOfWeek, $endOfWeek]);
        })->sum('porsi');
            
        $sisa_porsi_minggu_ini = max(0, $kapasitas_porsi_per_minggu - $jumlah_porsi_terjual_minggu_ini);
        
        $catering = $layanan; // pass back as $catering for view compatibility
        
        return view('admin.catering.show', compact(
            'catering', 
            'menus',
            'minumans',
            'kapasitas_porsi_per_minggu', 
            'jumlah_porsi_terjual_minggu_ini', 
            'sisa_porsi_minggu_ini',
            'tanggal_filter',
            'startOfWeek',
            'endOfWeek'
        ));
    }
}
