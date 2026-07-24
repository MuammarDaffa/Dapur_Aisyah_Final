<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\MenuHarian;
use Illuminate\Support\Facades\Storage;

class MenuHarianController extends Controller
{
    // =======================================
    // File : app/Http/Controllers/Admin/MenuHarianController.php
    // Fungsi : Menampilkan halaman form untuk menambah menu harian baru.
    // Dijalankan Kapan : Saat admin menekan tombol "+ Tambah Menu" di Card 2.
    // Data dikirim ke mana : resources/views/admin/menu-harian/create.blade.php
    // =======================================
    public function create(Layanan $layanan)
    {
        return view('admin.menu-harian.create', compact('layanan'));
    }

    // =======================================
    // Fungsi : Menyimpan data menu harian baru ke database.
    // Dijalankan Kapan : Saat admin menekan tombol "Simpan" di form tambah menu.
    // =======================================
    public function store(Request $request, Layanan $layanan)
    {
        // 1. Validasi input dari form
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean'
        ]);

        // 2. Hubungkan menu ini dengan layanan katering yang sedang dikelola
        $validated['layanan_id'] = $layanan->id;
        $validated['status'] = $request->boolean('status');

        // 3. Proses upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu_harian', 'public');
            $validated['gambar'] = $path;
        }

        // 4. Simpan ke database
        MenuHarian::create($validated);

        // 5. Kembali ke halaman manajemen harian dengan pesan sukses (SweetAlert akan dipicu oleh session 'success')
        return redirect()->route('admin.catering.harian', $layanan->id)
            ->with('success', 'Menu Harian berhasil ditambahkan!');
    }

    // =======================================
    // Fungsi : Menampilkan halaman form untuk mengedit menu harian.
    // Dijalankan Kapan : Saat admin menekan tombol "Edit" pada salah satu menu di Card 2.
    // =======================================
    public function edit(MenuHarian $menu)
    {
        // Mengirim data menu yang akan diedit ke form
        return view('admin.menu-harian.edit', compact('menu'));
    }

    // =======================================
    // Fungsi : Memperbarui data menu harian yang sudah ada di database.
    // Dijalankan Kapan : Saat admin menekan tombol "Update" di form edit menu.
    // =======================================
    public function update(Request $request, MenuHarian $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean'
        ]);

        $validated['status'] = $request->boolean('status');

        // Proses upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari folder jika gambar lama ada
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }
            // Simpan gambar baru
            $path = $request->file('gambar')->store('menu_harian', 'public');
            $validated['gambar'] = $path;
        }

        // Timpa data lama dengan data baru di database
        $menu->update($validated);

        // Kembali ke halaman manajemen harian katering milik menu ini
        return redirect()->route('admin.catering.harian', $menu->layanan_id)
            ->with('success', 'Menu Harian berhasil diupdate!');
    }

    // =======================================
    // Fungsi : Menghapus data menu harian dari database.
    // Dijalankan Kapan : Saat admin menekan "Ya, Hapus" pada SweetAlert konfirmasi.
    // =======================================
    public function destroy(MenuHarian $menu)
    {
        // Hapus gambar dari server jika ada
        if ($menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $layananId = $menu->layanan_id;

        // Hapus data dari tabel database
        // Karena di migration kita menggunakan cascadeOnDelete, jadwal_menu yang terhubung ke menu ini juga akan otomatis terhapus!
        $menu->delete();

        // Redirect kembali
        return redirect()->route('admin.catering.harian', $layananId)
            ->with('success', 'Menu Harian berhasil dihapus!');
    }
}
