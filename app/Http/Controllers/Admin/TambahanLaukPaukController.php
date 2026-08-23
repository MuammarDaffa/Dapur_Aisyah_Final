<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\TambahanLaukPauk;

class TambahanLaukPaukController extends Controller
{
    public function index(Menu $menu)
    {
        $items = $menu->tambahanLaukPauk;
        return view('admin.menu.tambahan_lauk_pauk', compact('menu', 'items'));
    }

    public function store(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0'
        ]);

        $validated['menu_id'] = $menu->id;
        TambahanLaukPauk::create($validated);

        return back()->with('success', 'Item berhasil ditambahkan!');
    }

    public function update(Request $request, TambahanLaukPauk $item)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0'
        ]);

        $item->update($validated);

        return back()->with('success', 'Item berhasil diupdate!');
    }

    public function destroy(TambahanLaukPauk $item)
    {
        $item->delete();
        return back()->with('success', 'Item berhasil dihapus!');
    }
}
