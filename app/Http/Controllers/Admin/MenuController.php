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
        if ($layanan->isAcara()) {
            $validated = $request->validate([
                'nama_menu' => 'required|string|max:100|unique:menu,nama_menu,NULL,id,layanan_id,' . $layanan->id,
            ]);
            $validated['layanan_id'] = $layanan->id;
            $validated['deskripsi'] = null;
            $validated['harga'] = 0;
            $validated['status'] = true;
        } else {
            $validated = $request->validate([
                'nama_menu' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'status' => 'boolean'
            ]);
            $validated['layanan_id'] = $layanan->id;
            $validated['status'] = $request->boolean('status');
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
        if ($menu->layanan->isAcara()) {
            $validated = $request->validate([
                'nama_menu' => 'required|string|max:100|unique:menu,nama_menu,' . $menu->id . ',id,layanan_id,' . $menu->layanan_id,
            ]);
            // we do not touch deskripsi, harga, status for Acara
        } else {
            $validated = $request->validate([
                'nama_menu' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'status' => 'boolean'
            ]);
            $validated['status'] = $request->boolean('status');
        }

        $menu->update($validated);

        if ($menu->layanan->isHarian()) {
            return redirect()->route('admin.catering.harian', $menu->layanan_id)->with('success', 'Menu berhasil diupdate!');
        }
        return redirect()->route('admin.catering.show', $menu->layanan_id)->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        $layananId = $menu->layanan_id;
        $isHarian = $menu->layanan->isHarian();
        $menu->delete();

        if ($isHarian) {
            return redirect()->route('admin.catering.harian', $layananId)->with('success', 'Menu berhasil dihapus!');
        }
        return redirect()->route('admin.catering.show', $layananId)->with('success', 'Menu berhasil dihapus!');
    }
}
