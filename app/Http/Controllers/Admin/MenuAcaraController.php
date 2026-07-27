<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\MenuAcara;
use App\Models\MinumanAcara;
use Illuminate\Http\Request;

class MenuAcaraController extends Controller
{
    /**
     * Menampilkan halaman Menu Acara (Menu Makanan & Minuman)
     */
    public function index($layananId)
    {
        $layanan = Layanan::findOrFail($layananId);
        $menus = MenuAcara::with('isiMenu')->where('layanan_id', $layananId)->get();
        // As per requirements, Minuman is standalone but typically linked to the system.
        // If Minuman doesn't belong to a specific Layanan, it shows all minuman. 
        // We will fetch all MinumanAcara.
        $minumans = MinumanAcara::all();

        return view('admin.catering.acara.index', compact('layanan', 'menus', 'minumans'));
    }

    /**
     * Menyimpan Menu Makanan Baru
     */
    public function storeMenu(Request $request, $layananId)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        MenuAcara::create([
            'layanan_id' => $layananId,
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'status' => true,
        ]);

        return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Menu Makanan berhasil ditambahkan.');
    }

    /**
     * Memperbarui Menu Makanan
     */
    public function updateMenu(Request $request, MenuAcara $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.catering.acara', $menu->layanan_id)->with('success', 'Menu Makanan berhasil diperbarui.');
    }

    /**
     * Menghapus Menu Makanan
     */
    public function destroyMenu(MenuAcara $menu)
    {
        $layananId = $menu->layanan_id;
        $menu->delete();

        return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Menu Makanan berhasil dihapus.');
    }

    /**
     * Menyimpan Minuman Baru
     */
    public function storeMinuman(Request $request, $layananId)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        MinumanAcara::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Minuman berhasil ditambahkan.');
    }

    /**
     * Memperbarui Minuman
     */
    public function updateMinuman(Request $request, MinumanAcara $minuman)
    {
        // To redirect back to the correct layanan, we need its ID from the request or previous URL
        $layananId = $request->input('layanan_id');

        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $minuman->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        if ($layananId) {
            return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Minuman berhasil diperbarui.');
        }
        return back()->with('success', 'Minuman berhasil diperbarui.');
    }

    /**
     * Menghapus Minuman
     */
    public function destroyMinuman(Request $request, MinumanAcara $minuman)
    {
        $layananId = $request->input('layanan_id');
        $minuman->delete();

        if ($layananId) {
            return redirect()->route('admin.catering.acara', $layananId)->with('success', 'Minuman berhasil dihapus.');
        }
        return back()->with('success', 'Minuman berhasil dihapus.');
    }
}
