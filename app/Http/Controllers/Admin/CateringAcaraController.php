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

        $layanan->load(['menus.items', 'minumans']);
        $menus = $layanan->menus;
        $minumans = $layanan->minumans;
        
        $catering = $layanan; // pass back as $catering for view compatibility
        
        return view('admin.catering.show', compact(
            'catering', 
            'menus',
            'minumans'
        ));
    }
}
