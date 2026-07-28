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
    public function show(Layanan $catering)
    {
        if ($catering->isAcara()) {
            $catering->load(['menus.items']);
            $menus = $catering->menus;
            return view('admin.catering.show', compact('catering', 'menus'));
        }

        // Mengambil data satu katering dari database (melalui model Layanan) dan mengirimkannya ke file view.
        return view('admin.catering.show', compact('catering'));
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
