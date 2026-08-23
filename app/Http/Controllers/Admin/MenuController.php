<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function create(string $tipe_layanan)
    {
        return view('admin.menu.create', compact('tipe_layanan'));
    }

    public function store(Request $request, string $tipe_layanan)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'harga' => 'required|numeric|min:0'
        ]);

        $validated['tipe_layanan'] = $tipe_layanan;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('menu', $filename, 'public');
            $validated['gambar'] = $filename;
        }

        Menu::create($validated);

        if ($tipe_layanan === 'harian') {
            return redirect()->route('admin.catering.harian')->with('swal_success', 'Data berhasil ditambahkan.');
        }
        return redirect()->route('admin.catering.acara')->with('swal_success', 'Data berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga' => 'required|numeric|min:0'
        ]);

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                Storage::disk('public')->delete('menu/' . $menu->gambar);
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('menu', $filename, 'public');
            $validated['gambar'] = $filename;
        }

        $menu->update($validated);

        if ($menu->tipe_layanan === 'harian') {
            return redirect()->route('admin.catering.harian')->with('swal_success', 'Data berhasil diupdate.');
        }
        return redirect()->route('admin.catering.acara')->with('swal_success', 'Data berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $tipeLayanan = $menu->tipe_layanan;
        
        if ($menu->gambar) {
            Storage::disk('public')->delete('menu/' . $menu->gambar);
        }
        
        $menu->delete();

        if ($tipeLayanan === 'harian') {
            return redirect()->route('admin.catering.harian');
        }
        return redirect()->route('admin.catering.acara');
    }
}
