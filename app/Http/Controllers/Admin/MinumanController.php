<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Layanan removed
use App\Models\Minuman;

class MinumanController extends Controller
{
    public function store(Request $request, string $tipe_layanan = 'acara')
    {
        $validated = $request->validate([
            'nama_minuman' => 'required|string|max:100|unique:minumans,nama_minuman,NULL,id,tipe_layanan,' . $tipe_layanan,
            'harga' => 'required|numeric|min:0',
        ]);

        $validated['tipe_layanan'] = $tipe_layanan;

        Minuman::create($validated);

        return redirect()->route('admin.catering.acara')->with('success', 'Minuman berhasil ditambahkan!');
    }

    public function update(Request $request, Minuman $minuman)
    {
        $validated = $request->validate([
            'nama_minuman' => 'required|string|max:100|unique:minumans,nama_minuman,' . $minuman->id . ',id,tipe_layanan,' . $minuman->tipe_layanan,
            'harga' => 'required|numeric|min:0',
        ]);

        $minuman->update($validated);

        return redirect()->route('admin.catering.acara')->with('success', 'Minuman berhasil diupdate!');
    }

    public function destroy(Minuman $minuman)
    {
        $minuman->delete();

        return redirect()->route('admin.catering.acara')->with('success', 'Minuman berhasil dihapus!');
    }
}
