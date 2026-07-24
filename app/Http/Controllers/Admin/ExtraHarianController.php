<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalMenu;
use App\Models\ExtraHarian;

class ExtraHarianController extends Controller
{
    // =======================================
    // File : app/Http/Controllers/Admin/ExtraHarianController.php
    // Fungsi : Menampilkan halaman kelola Extra Menu berdasarkan jadwal tertentu.
    // Dijalankan Kapan : Saat admin menekan tombol "Kelola" di kolom Extra pada Card 1 (Jadwal).
    // Data berasal dari mana : Model JadwalMenu (dan relasinya ke ExtraHarian).
    // Data dikirim ke mana : resources/views/admin/extra-harian/index.blade.php
    // =======================================
    public function index(JadwalMenu $jadwal)
    {
        // Mengambil seluruh data extra yang terkait dengan jadwal ini
        $extras = ExtraHarian::where('jadwal_menu_id', $jadwal->id)->get();

        return view('admin.extra-harian.index', compact('jadwal', 'extras'));
    }

    // =======================================
    // Fungsi : Menampilkan form untuk menambah extra baru.
    // Dijalankan Kapan : Saat admin menekan tombol "Tambah Extra".
    // =======================================
    public function create(JadwalMenu $jadwal)
    {
        return view('admin.extra-harian.create', compact('jadwal'));
    }

    // =======================================
    // Fungsi : Menyimpan data extra baru ke database.
    // Dijalankan Kapan : Saat admin menekan tombol "Simpan" di form tambah extra.
    // =======================================
    public function store(Request $request, JadwalMenu $jadwal)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        // 2. Hubungkan extra dengan jadwal_menu ini
        $validated['jadwal_menu_id'] = $jadwal->id;

        // 3. Simpan ke database
        ExtraHarian::create($validated);

        // 4. Redirect ke halaman index extra dengan pesan sukses
        return redirect()->route('admin.extra-harian.index', $jadwal->id)
            ->with('success', 'Extra Menu berhasil ditambahkan!');
    }

    // =======================================
    // Fungsi : Menampilkan form untuk mengedit extra yang sudah ada.
    // Dijalankan Kapan : Saat admin menekan tombol "Edit" pada salah satu extra.
    // =======================================
    public function edit(ExtraHarian $extra)
    {
        return view('admin.extra-harian.edit', compact('extra'));
    }

    // =======================================
    // Fungsi : Memperbarui data extra di database.
    // Dijalankan Kapan : Saat admin menekan tombol "Update".
    // =======================================
    public function update(Request $request, ExtraHarian $extra)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        // Timpa data lama dengan data baru
        $extra->update($validated);

        // Redirect kembali ke daftar extra milik jadwal ini
        return redirect()->route('admin.extra-harian.index', $extra->jadwal_menu_id)
            ->with('success', 'Extra Menu berhasil diupdate!');
    }

    // =======================================
    // Fungsi : Menghapus extra dari database.
    // Dijalankan Kapan : Saat admin mengkonfirmasi penghapusan (Ya, Hapus) pada SweetAlert.
    // =======================================
    public function destroy(ExtraHarian $extra)
    {
        $jadwalId = $extra->jadwal_menu_id;

        // Hapus data
        $extra->delete();

        // Redirect kembali
        return redirect()->route('admin.extra-harian.index', $jadwalId)
            ->with('success', 'Extra Menu berhasil dihapus!');
    }
}
