<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class CateringController extends Controller
{
    /**
     * Daftar semua layanan katering
     */
    public function index(Request $request)
    {
        $query = Layanan::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $caterings = $query->latest()->paginate(10);

        return view('admin.catering.index', compact('caterings'));
    }

    /**
     * Form tambah katering baru.
     */
    public function create()
    {
        return view('admin.catering.create');
    }

    /**
     * Simpan katering baru.
     */
    public function store(Request $request)
    {
        $isHarian = $request->input('tipe') === 'harian';
        
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'tipe' => 'required|in:harian,acara',
            'kapasitas_porsi_per_minggu' => $isHarian ? 'nullable' : 'required|integer|min:1',
            'status' => 'boolean',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['status'] = $request->boolean('status');

        if ($isHarian) {
            $validated['kapasitas_porsi_per_minggu'] = null;
        }

        Layanan::create($validated);

        return redirect()->route('admin.catering.index')->with('success', 'Layanan Katering berhasil ditambahkan.');
    }

    // =======================================
    // File : app/Http/Controllers/Admin/CateringController.php
    // Fungsi : Menampilkan halaman detail spesifik dari satu katering.
    // Dijalankan Kapan : Ketika admin menekan tombol ikon mata (Detail) di halaman daftar katering.
    // Data berasal dari mana : Model Layanan berdasarkan ID yang diklik.
    // Data dikirim ke mana : Halaman resources/views/admin/catering/show.blade.php
    // Apa yang terjadi jika dihapus : Tombol detail akan error (method not found).
    // =======================================
    public function show(Request $request, Layanan $catering)
    {
        $kapasitas_porsi_per_minggu = $catering->kapasitas_porsi_per_minggu ?? 0;
        $jumlah_porsi_terjual_minggu_ini = 0;
        $sisa_porsi_minggu_ini = $kapasitas_porsi_per_minggu;

        // Ambil tanggal filter dari request (default: hari ini)
        $tanggal_filter = $request->input('tanggal', now()->format('Y-m-d'));
        $startOfWeek = \Carbon\Carbon::parse($tanggal_filter)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($tanggal_filter)->endOfWeek()->format('Y-m-d');

        if ($catering->isAcara()) {
            $catering->load(['menus.items', 'minumans']);
            $menus = $catering->menus;
            $minumans = $catering->minumans;
            
            // Perhatikan: Kita menghitung berdasar tanggal_pesanan (kapan acara berlangsung), bukan created_at
            $jumlah_porsi_terjual_minggu_ini = (int) \App\Models\DetailPesanan::whereHas('pesanan', function($q) use ($catering, $startOfWeek, $endOfWeek) {
                $q->where('layanan_id', $catering->id)
                  ->where('status_pesanan', '!=', 'dibatalkan')
                  ->whereBetween('tanggal_pesanan', [$startOfWeek, $endOfWeek]);
            })->sum('porsi');
                
            $sisa_porsi_minggu_ini = max(0, $kapasitas_porsi_per_minggu - $jumlah_porsi_terjual_minggu_ini);
            
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

        return view('admin.catering.show', compact(
            'catering',
            'kapasitas_porsi_per_minggu',
            'jumlah_porsi_terjual_minggu_ini',
            'sisa_porsi_minggu_ini',
            'tanggal_filter',
            'startOfWeek',
            'endOfWeek'
        ));
    }

    /**
     * Proses pembaruan data katering.
     */
    public function update(Request $request, Layanan $catering)
    {
        $isHarian = $request->input('tipe') === 'harian';
        
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'tipe' => 'required|in:harian,acara',
            'kapasitas_porsi_per_minggu' => $isHarian ? 'nullable' : 'required|integer|min:1',
            'status' => 'boolean',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['status'] = $request->boolean('status');

        if ($isHarian) {
            $validated['kapasitas_porsi_per_minggu'] = null;
        }

        $catering->update($validated);

        return redirect()->route('admin.catering.show', $catering->id)->with('success', 'Layanan Katering berhasil diperbarui.');
    }


    public function destroy(Layanan $catering)
    {
        $catering->delete();
        
        return redirect()->route('admin.catering.index')->with('success', 'Layanan Katering berhasil dihapus.');
    }
}
