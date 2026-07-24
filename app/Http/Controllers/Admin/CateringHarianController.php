<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\MenuHarian;
use App\Models\JadwalMenu;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CateringHarianController extends Controller
{
    // =======================================
    // File : app/Http/Controllers/Admin/CateringHarianController.php
    // Fungsi : Menampilkan halaman Manajemen Katering Harian (Card 1 & Card 2).
    // Dijalankan Kapan : Saat admin menekan tombol Manajemen Harian (ikon mata) di tabel katering.
    // Data berasal dari mana : Model Layanan, MenuHarian, dan JadwalMenu.
    // Data dikirim ke mana : View resources/views/admin/catering/harian.blade.php
    // =======================================
    public function index(Request $request, Layanan $layanan)
    {
        // Memastikan katering yang dibuka benar-benar tipe harian
        if (!$layanan->isHarian()) {
            return redirect()->route('admin.catering.index')->with('error', 'Layanan ini bukan tipe Harian.');
        }

        // 1. Mengambil daftar menu harian milik katering ini (Untuk mengisi form dropdown jadwal dan Card 2)
        $daftarMenu = MenuHarian::where('layanan_id', $layanan->id)->get();

        // 2. Mengambil data jadwal menu yang sudah tersimpan sebelumnya (jika ada)
        $menuIds = $daftarMenu->pluck('id');
        $jadwalTersimpan = JadwalMenu::with('extraHarian')->whereIn('menu_harian_id', $menuIds)->get()->keyBy('tanggal');

        // Mengambil rentang tanggal dari request (jika ada)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if (!$startDate && !$endDate) {
            $jadwalPalingAwal = JadwalMenu::whereIn('menu_harian_id', $menuIds)->orderBy('tanggal', 'asc')->first();
            $jadwalPalingAkhir = JadwalMenu::whereIn('menu_harian_id', $menuIds)->orderBy('tanggal', 'desc')->first();
            
            if ($jadwalPalingAwal && $jadwalPalingAkhir) {
                $startDate = $jadwalPalingAwal->tanggal->format('Y-m-d');
                $endDate = $jadwalPalingAkhir->tanggal->format('Y-m-d');
            }
        }

        $daftarTanggal = [];

        if ($startDate && $endDate) {
            Carbon::setLocale('id');
            $period = CarbonPeriod::create($startDate, $endDate);
            
            foreach ($period as $date) {
                $daftarTanggal[] = [
                    'tanggal' => $date->format('Y-m-d'),
                    'hari' => $date->translatedFormat('l')
                ];
            }
        }

        return view('admin.catering.harian', compact('layanan', 'daftarMenu', 'jadwalTersimpan', 'daftarTanggal', 'startDate', 'endDate'));
    }

    // =======================================
    // Fungsi : Menyimpan atau memperbarui data Pengaturan Jadwal Menu (Card 1).
    // Dijalankan Kapan : Saat admin menekan tombol "Simpan Jadwal" di bawah tabel jadwal.
    // Data berasal dari mana : Form di halaman harian.blade.php
    // Mengapa ini diperlukan : Untuk mengatur menu apa saja yang tersedia di hari tertentu beserta stok awalnya.
    // =======================================
    public function updateJadwal(Request $request, Layanan $layanan)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $jadwalInput = $request->input('jadwal', []);

        if (!$startDate || !$endDate) {
            return back()->with('error', 'Rentang tanggal tidak ditemukan, silakan generate ulang.');
        }

        Carbon::setLocale('id');
        $period = CarbonPeriod::create($startDate, $endDate);
        
        $daftarTanggal = [];
        foreach ($period as $date) {
            $daftarTanggal[] = $date->format('Y-m-d');
        }

        // Melakukan proses validasi tanggal terlebih dahulu sebelum menyimpan data apapun
        foreach ($daftarTanggal as $keyTanggal) {
            $input = $jadwalInput[$keyTanggal] ?? [];
            // Jika hari tersebut dicentang aktif oleh admin
            if (isset($input['aktif']) && $input['aktif'] == '1') {
                $tanggalInput = $input['tanggal'] ?? null;
                $menuId = $input['menu_harian_id'] ?? null;

                // Memastikan data tanggal dan menu diisi
                if (!$tanggalInput || !$menuId) {
                    $hariError = Carbon::parse($keyTanggal)->translatedFormat('l');
                    return back()->with('error', "Tanggal dan Menu pada hari $hariError harus diisi jika diaktifkan.");
                }

                // Memastikan input tanggal sesuai dengan barisnya
                if ($keyTanggal !== $tanggalInput) {
                    return back()->with('error', "Validasi Gagal: Tanggal yang dipilih tidak sesuai.");
                }
            }
        }

        // Jika lolos validasi, kita mulai proses penyimpanan (menyimpan/mengubah/menghapus)
        foreach ($daftarTanggal as $keyTanggal) {
            $input = $jadwalInput[$keyTanggal] ?? [];
            $isAktif = isset($input['aktif']) && $input['aktif'] == '1';
            
            $namaHari = Carbon::parse($keyTanggal)->translatedFormat('l');

            // Coba cari apakah sebelumnya jadwal hari ini sudah pernah dibuat
            // Kita cari dari daftar menu harian milik layanan ini
            $menuIds = MenuHarian::where('layanan_id', $layanan->id)->pluck('id');
            $jadwalLama = JadwalMenu::whereIn('menu_harian_id', $menuIds)->where('tanggal', $keyTanggal)->first();

            if ($isAktif) {
                // Jika aktif, kita update (jika ada) atau create (jika belum ada)
                if ($jadwalLama) {
                    // Update data jadwal lama
                    $jadwalLama->update([
                        'menu_harian_id' => $input['menu_harian_id'],
                        'tanggal' => $input['tanggal'],
                        'hari' => $namaHari,
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0, // Direset sama dengan stok awal jika diubah
                        'aktif' => true
                    ]);
                    $activeJadwal = $jadwalLama;
                } else {
                    // Create data jadwal baru
                    $activeJadwal = JadwalMenu::create([
                        'menu_harian_id' => $input['menu_harian_id'],
                        'hari' => $namaHari,
                        'tanggal' => $input['tanggal'],
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0,
                        'aktif' => true
                    ]);
                }

                // --- Sinkronisasi Extra Harian ---
                $submittedExtras = $input['extras'] ?? [];
                
                // Kumpulkan ID extra yang disubmit
                $submittedIds = collect($submittedExtras)->pluck('id')->filter()->toArray();
                
                // Hapus extra yang ada di database tapi tidak ada di form submission (berarti dihapus oleh admin di modal)
                $activeJadwal->extraHarian()->whereNotIn('id', $submittedIds)->delete();
                
                // Create atau Update extra yang disubmit
                foreach ($submittedExtras as $extraInput) {
                    if (isset($extraInput['id']) && $extraInput['id'] != '') {
                        $activeJadwal->extraHarian()->where('id', $extraInput['id'])->update([
                            'nama' => $extraInput['nama'],
                            'harga' => $extraInput['harga']
                        ]);
                    } else {
                        $activeJadwal->extraHarian()->create([
                            'nama' => $extraInput['nama'],
                            'harga' => $extraInput['harga']
                        ]);
                    }
                }

            } else {
                // Jika tidak aktif (tidak dicentang), berarti admin ingin meliburkan/menghapus jadwal hari tersebut
                if ($jadwalLama) {
                    $jadwalLama->delete();
                }
            }
        }

        // Kembali ke halaman sebelumnya dengan parameter pencarian dan pesan sukses
        return redirect()->route('admin.catering.harian', [
            'layanan' => $layanan->id,
            'start_date' => $startDate,
            'end_date' => $endDate
        ])->with('success', 'Jadwal Menu berhasil diperbarui.');
    }
}
