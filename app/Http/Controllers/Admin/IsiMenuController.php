<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuAcara;
use App\Models\IsiMenu;
use Illuminate\Http\Request;

class IsiMenuController extends Controller
{
    /**
     * Menampilkan halaman kelola Isi Menu
     */
    public function index($menuId)
    {
        $menu = MenuAcara::with('isiMenu')->findOrFail($menuId);
        $isiMenus = $menu->isiMenu;

        return view('admin.catering.acara.isi_menu', compact('menu', 'isiMenus'));
    }

    /**
     * Menyimpan Isi Menu Baru
     */
    public function store(Request $request, MenuAcara $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        IsiMenu::create([
            'menu_acara_id' => $menu->id,
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('admin.isi-menu.index', $menu->id)->with('success', 'Isi Menu berhasil ditambahkan.');
    }

    /**
     * Memperbarui Isi Menu
     */
    public function update(Request $request, IsiMenu $isi)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $isi->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('admin.isi-menu.index', $isi->menu_acara_id)->with('success', 'Isi Menu berhasil diperbarui.');
    }

    /**
     * Menghapus Isi Menu
     */
    public function destroy(IsiMenu $isi)
    {
        $menuId = $isi->menu_acara_id;
        $isi->delete();

        return redirect()->route('admin.isi-menu.index', $menuId)->with('success', 'Isi Menu berhasil dihapus.');
    }
}
