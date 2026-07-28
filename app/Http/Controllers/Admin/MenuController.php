<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function create(Layanan $layanan)
    {
        return view('admin.menu.create', compact('layanan'));
    }

    public function store(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean'
        ]);

        $validated['layanan_id'] = $layanan->id;
        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu', 'public');
            $validated['gambar'] = $path;
        }

        Menu::create($validated);

        if ($layanan->isHarian()) {
            return redirect()->route('admin.catering.harian', $layanan->id)->with('success', 'Menu berhasil ditambahkan!');
        }
        return redirect()->route('admin.catering.show', $layanan->id)->with('success', 'Menu berhasil ditambahkan!');
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean'
        ]);

        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $path = $request->file('gambar')->store('menu', 'public');
            $validated['gambar'] = $path;
        }

        $menu->update($validated);

        if ($menu->layanan->isHarian()) {
            return redirect()->route('admin.catering.harian', $menu->layanan_id)->with('success', 'Menu berhasil diupdate!');
        }
        return redirect()->route('admin.catering.show', $menu->layanan_id)->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $layananId = $menu->layanan_id;
        $isHarian = $menu->layanan->isHarian();
        $menu->delete();

        if ($isHarian) {
            return redirect()->route('admin.catering.harian', $layananId)->with('success', 'Menu berhasil dihapus!');
        }
        return redirect()->route('admin.catering.show', $layananId)->with('success', 'Menu berhasil dihapus!');
    }
}
