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
            'kapasitas_total' => $isHarian ? 'nullable' : 'required|integer|min:1',
            'minimal_porsi' => $isHarian ? 'nullable' : 'required|integer|min:1',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        if ($isHarian) {
            $validated['kapasitas_total'] = null;
            $validated['kapasitas_tersisa'] = null;
            $validated['minimal_porsi'] = null;
        } else {
            $validated['kapasitas_tersisa'] = $validated['kapasitas_total'];
        }

        Layanan::create($validated);

        return redirect()->route('admin.catering.index')->with('success', 'Layanan Katering berhasil ditambahkan.');
    }

    
}
