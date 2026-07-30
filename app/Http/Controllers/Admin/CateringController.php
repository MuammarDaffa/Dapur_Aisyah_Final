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
            'status' => 'boolean',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['status'] = $request->boolean('status');

        Layanan::create($validated);

        return redirect()->route('admin.catering.index')->with('success', 'Layanan Katering berhasil ditambahkan.');
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
            'status' => 'boolean',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['status'] = $request->boolean('status');

        $catering->update($validated);

        if ($catering->isHarian()) {
            return redirect()->route('admin.catering.harian', $catering->id)->with('success', 'Layanan Katering berhasil diperbarui.');
        } else {
            return redirect()->route('admin.catering.acara', $catering->id)->with('success', 'Layanan Katering berhasil diperbarui.');
        }
    }


    public function destroy(Layanan $catering)
    {
        $catering->delete();
        
        return redirect()->route('admin.catering.index')->with('success', 'Layanan Katering berhasil dihapus.');
    }
}
