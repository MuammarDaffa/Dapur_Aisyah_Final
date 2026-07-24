<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\MenuHarian;
use App\Models\JadwalMenu;
use Carbon\Carbon;

class CateringHarianController extends Controller
{
    // =======================================
    // File : app/Http/Controllers/Admin/CateringHarianController.php
    // Fungsi : Menampilkan halaman Manajemen Katering Harian (Card 1 & Card 2).
    // Dijalankan Kapan : Saat admin menekan tombol Manajemen Harian (ikon mata) di tabel katering.
    // Data berasal dari mana : Model Layanan, MenuHarian, dan JadwalMenu.
    // Data dikirim ke mana : View resources/views/admin/catering/harian.blade.php
    // =======================================
    public function index(Layanan $layanan)
    {
        // Memastikan katering yang dibuka benar-benar tipe harian
        if (!$layanan->isHarian()) {
            return redirect()->route('admin.catering.index')->with('error', 'Layanan ini bukan tipe Harian.');
        }

        // 1. Mengambil daftar menu harian milik katering ini (Untuk mengisi form dropdown jadwal dan Card 2)
        $daftarMenu = MenuHarian::where('layanan_id', $layanan->id)->get();

        // 2. Mengambil data jadwal menu yang sudah tersimpan sebelumnya (jika ada)
        // Kita menggunakan array dengan key (kunci) nama hari agar mudah dicocokkan di tabel View
        // Karena jadwal_menu terhubung via menu_harian_id, kita ambil jadwal yang terkait dengan menu milik layanan ini.
        $menuIds = $daftarMenu->pluck('id');
        $jadwalTersimpan = JadwalMenu::whereIn('menu_harian_id', $menuIds)->get()->keyBy('hari');

        // Daftar hari paten (Senin sampai Jumat)
        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('admin.catering.harian', compact('layanan', 'daftarMenu', 'jadwalTersimpan', 'daftarHari'));
    }

    // =======================================
    // Fungsi : Menyimpan atau memperbarui data Pengaturan Jadwal Menu (Card 1).
    // Dijalankan Kapan : Saat admin menekan tombol "Simpan Jadwal" di bawah tabel jadwal.
    // Data berasal dari mana : Form di halaman harian.blade.php
    // Mengapa ini diperlukan : Untuk mengatur menu apa saja yang tersedia di hari tertentu beserta stok awalnya.
    // =======================================
    public function updateJadwal(Request $request, Layanan $layanan)
    {
        // Array hari yang diizinkan untuk divalidasi
        $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Peta (Mapping) nama hari bahasa Inggris (dari Carbon) ke bahasa Indonesia
        $mapHari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $jadwalInput = $request->input('jadwal', []);

        // Melakukan proses validasi tanggal terlebih dahulu sebelum menyimpan data apapun
        foreach ($hariValid as $hari) {
            // Jika hari tersebut dicentang aktif oleh admin
            if (isset($jadwalInput[$hari]['aktif']) && $jadwalInput[$hari]['aktif'] == '1') {
                $tanggalInput = $jadwalInput[$hari]['tanggal'] ?? null;
                $menuId = $jadwalInput[$hari]['menu_harian_id'] ?? null;

                // Memastikan data tanggal dan menu diisi
                if (!$tanggalInput || !$menuId) {
                    return back()->with('error', "Tanggal dan Menu pada hari $hari harus diisi jika diaktifkan.");
                }

                // Mengambil nama hari dari tanggal yang dipilih menggunakan Carbon
                $namaHariInggris = Carbon::parse($tanggalInput)->format('l');
                $namaHariIndonesia = $mapHari[$namaHariInggris] ?? '';

                // Mencocokkan apakah tanggal yang dipilih (misal 13 Agt = Selasa) sesuai dengan barisnya (misal Senin)
                if ($namaHariIndonesia !== $hari) {
                    return back()->with('error', "Validasi Gagal: Tanggal yang dipilih pada baris $hari ternyata adalah hari $namaHariIndonesia.");
                }
            }
        }

        // Jika lolos validasi, kita mulai proses penyimpanan (menyimpan/mengubah/menghapus)
        foreach ($hariValid as $hari) {
            $input = $jadwalInput[$hari] ?? [];
            $isAktif = isset($input['aktif']) && $input['aktif'] == '1';

            // Coba cari apakah sebelumnya jadwal hari ini sudah pernah dibuat
            // Kita cari dari daftar menu harian milik layanan ini
            $menuIds = MenuHarian::where('layanan_id', $layanan->id)->pluck('id');
            $jadwalLama = JadwalMenu::whereIn('menu_harian_id', $menuIds)->where('hari', $hari)->first();

            if ($isAktif) {
                // Jika aktif, kita update (jika ada) atau create (jika belum ada)
                if ($jadwalLama) {
                    // Update data jadwal lama
                    $jadwalLama->update([
                        'menu_harian_id' => $input['menu_harian_id'],
                        'tanggal' => $input['tanggal'],
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0, // Direset sama dengan stok awal jika diubah
                        'aktif' => true
                    ]);
                } else {
                    // Create data jadwal baru
                    JadwalMenu::create([
                        'menu_harian_id' => $input['menu_harian_id'],
                        'hari' => $hari,
                        'tanggal' => $input['tanggal'],
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0,
                        'aktif' => true
                    ]);
                }
            } else {
                // Jika tidak aktif (tidak dicentang), berarti admin ingin meliburkan/menghapus jadwal hari tersebut
                if ($jadwalLama) {
                    $jadwalLama->delete();
                }
            }
        }

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Jadwal Menu berhasil diperbarui.');
    }
}
