<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Layanan removed
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
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'status' => 'boolean',
            'kategori_penyajian' => $tipe_layanan === 'acara' ? 'required|in:bisa_pilih,prasmanan_saja' : 'nullable|string'
        ]);

        $validated['tipe_layanan'] = $tipe_layanan;
        $validated['status'] = $request->boolean('status');

        Menu::create($validated);

        if ($tipe_layanan === 'harian') {
            return redirect()->route('admin.catering.harian')->with('success', 'Menu berhasil ditambahkan!');
        }
        return redirect()->route('admin.catering.acara')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'status' => 'boolean',
            'kategori_penyajian' => $menu->tipe_layanan === 'acara' ? 'required|in:bisa_pilih,prasmanan_saja' : 'nullable|string'
        ]);

        $validated['status'] = $request->boolean('status');

        $menu->update($validated);

        if ($menu->tipe_layanan === 'harian') {
            return redirect()->route('admin.catering.harian')->with('success', 'Menu berhasil diupdate!');
        }
        return redirect()->route('admin.catering.acara')->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        $tipeLayanan = $menu->tipe_layanan;
        $menu->delete();

        if ($tipeLayanan === 'harian') {
            return redirect()->route('admin.catering.harian')->with('success', 'Menu berhasil dihapus!');
        }
        return redirect()->route('admin.catering.acara')->with('success', 'Menu berhasil dihapus!');
    }
}
