<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuItemController extends Controller
{
    public function index(Menu $menu)
    {
        // Untuk Acara: melihat detail menu acara dan menambah isi menu
        // Untuk Harian: bisa jadi melihat detail menu harian dan menambah extra
        $items = $menu->items;
        return view('admin.menu.items', compact('menu', 'items'));
    }

    public function store(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0'
        ]);

        $validated['menu_id'] = $menu->id;
        MenuItem::create($validated);

        return back()->with('success', 'Item berhasil ditambahkan!');
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0'
        ]);

        $item->update($validated);

        return back()->with('success', 'Item berhasil diupdate!');
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item berhasil dihapus!');
    }
}
