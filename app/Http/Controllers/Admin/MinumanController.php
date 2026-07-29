<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Minuman;

class MinumanController extends Controller
{
    public function store(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            'nama_minuman' => 'required|string|max:100|unique:minumans,nama_minuman,NULL,id,layanan_id,' . $layanan->id,
            'harga' => 'required|numeric|min:0',
        ]);

        $validated['layanan_id'] = $layanan->id;

        Minuman::create($validated);

        return redirect()->route('admin.catering.acara', $layanan->id)->with('success', 'Minuman berhasil ditambahkan!');
    }

    public function update(Request $request, Minuman $minuman)
    {
        $validated = $request->validate([
            'nama_minuman' => 'required|string|max:100|unique:minumans,nama_minuman,' . $minuman->id . ',id,layanan_id,' . $minuman->layanan_id,
            'harga' => 'required|numeric|min:0',
        ]);

        $minuman->update($validated);

        return redirect()->route('admin.catering.acara', $minuman->layanan_id)->with('success', 'Minuman berhasil diupdate!');
    }

    public function destroy(Minuman $minuman)
    {
        $layananId = $minuman->layanan_id;
        $minuman->delete();

        return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Minuman berhasil dihapus!');
    }
}
