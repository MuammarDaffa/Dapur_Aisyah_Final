<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananKatering;
use App\Models\MenuHarian;
use App\Models\MenuHarianExtra;
use Illuminate\Http\Request;

class MenuHarianController extends Controller
{
    /**
     * Update Batch (Penyimpanan dari tabel 5 baris)
     */
    public function updateBatch(Request $request, LayananKatering $catering)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'menus.*.tanggal' => 'nullable|date',
            'menus.*.nama_menu' => 'nullable|string|required_with:menus.*.tanggal',
            'menus.*.harga' => 'nullable|numeric|min:0|required_with:menus.*.tanggal',
            'menus.*.stok_awal' => 'nullable|integer|min:1|required_with:menus.*.tanggal',
        ], [
            'menus.*.nama_menu.required_with' => 'Nama Menu wajib diisi jika tanggal diisi.',
            'menus.*.harga.required_with' => 'Harga wajib diisi jika tanggal diisi.',
            'menus.*.stok_awal.required_with' => 'Stok Awal wajib diisi jika tanggal diisi.',
        ]);

        foreach ($request->menus as $menuData) {
            // Jika tanggal kosong, baris ini dilewati (tidak disimpan)
            if (empty($menuData['tanggal'])) {
                continue;
            }

            // Simpan atau update
            MenuHarian::updateOrCreate(
                [
                    'layanan_katering_id' => $catering->id,
                    'hari' => $menuData['hari'],
                ],
                [
                    'tanggal' => $menuData['tanggal'],
                    'nama_menu' => $menuData['nama_menu'],
                    'harga' => $menuData['harga'],
                    'stok_awal' => $menuData['stok_awal'],
                    'stok_tersisa' => $menuData['stok_awal'], // reset stok tersisa ke stok awal saat diupdate
                ]
            );
        }

        return redirect()->back()->with('success', 'Menu Harian berhasil disimpan.');
    }

    /**
     * Tampilkan halaman kelola Extra
     */
    public function extraIndex(MenuHarian $menuHarian)
    {
        // Load relasi extras
        $menuHarian->load('extras');
        
        return view('admin.menu_harian.extra.index', compact('menuHarian'));
    }

    /**
     * Simpan extra baru
     */
    public function extraStore(Request $request, MenuHarian $menuHarian)
    {
        $validated = $request->validate([
            'nama_extra' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $menuHarian->extras()->create($validated);

        return redirect()->route('admin.menu-harian.extra.index', $menuHarian)
            ->with('success', 'Extra berhasil ditambahkan.');
    }

    /**
     * Edit extra
     */
    public function extraEdit(MenuHarian $menuHarian, MenuHarianExtra $extra)
    {
        return view('admin.menu_harian.extra.edit', compact('menuHarian', 'extra'));
    }

    /**
     * Update extra
     */
    public function extraUpdate(Request $request, MenuHarian $menuHarian, MenuHarianExtra $extra)
    {
        $validated = $request->validate([
            'nama_extra' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $extra->update($validated);

        return redirect()->route('admin.menu-harian.extra.index', $menuHarian)
            ->with('success', 'Extra berhasil diperbarui.');
    }

    /**
     * Hapus extra
     */
    public function extraDestroy(MenuHarian $menuHarian, MenuHarianExtra $extra)
    {
        $extra->delete();

        return redirect()->route('admin.menu-harian.extra.index', $menuHarian)
            ->with('success', 'Extra berhasil dihapus.');
    }
}
