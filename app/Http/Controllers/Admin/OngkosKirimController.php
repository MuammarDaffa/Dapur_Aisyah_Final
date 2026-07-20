<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\OngkosKirim;
use Illuminate\Http\Request;

class OngkosKirimController extends Controller
{
    public function index()
    {
        $ongkosKirim = OngkosKirim::with('kecamatan')->get();
        $kecamatan = Kecamatan::all();
        return view('admin.ongkos_kirim.index', compact('ongkosKirim', 'kecamatan'));
    }

    public function create()
    {
        $kecamatan = Kecamatan::all();
        return view('admin.ongkos_kirim.create', compact('kecamatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatan,id|unique:ongkos_kirim,kecamatan_id',
            'cost' => 'required|numeric|min:0|max:1000000000',
            'catatan' => 'nullable|string|max:255',
        ], [
            'cost.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);
        OngkosKirim::create($validated);
        return redirect()->route('admin.ongkos_kirim.index')->with('success', 'Ongkos kirim berhasil ditambahkan.');
    }

    public function edit(OngkosKirim $shipping)
    {
        $kecamatan = Kecamatan::all();
        return view('admin.ongkos_kirim.edit', compact('shipping', 'kecamatan'));
    }

    public function update(Request $request, OngkosKirim $shipping)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'cost' => 'required|numeric|min:0|max:1000000000',
            'catatan' => 'nullable|string|max:255',
        ], [
            'cost.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);
        $shipping->update($validated);
        if (!$shipping->wasChanged()) {
            return redirect()->route('admin.ongkos_kirim.index');
        }
        return redirect()->route('admin.ongkos_kirim.index')->with('success', 'Ongkos kirim berhasil diperbarui.');
    }

    public function destroy(OngkosKirim $shipping)
    {
        $shipping->delete();
        return redirect()->route('admin.ongkos_kirim.index')->with('success', 'Ongkos kirim berhasil dihapus.');
    }
}
