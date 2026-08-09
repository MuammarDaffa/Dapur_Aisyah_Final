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

        $menus = \App\Models\Menu::with('tambahanLaukPauk')->where('tipe_layanan', 'acara')->get();
        $minumans = \App\Models\Minuman::where('tipe_layanan', 'acara')->get();
        
        return view('admin.catering.show', compact(
            'menus',
            'minumans'
        ));
    }
}
