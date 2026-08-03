<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// App\Models\Layanan removed
use App\Models\Menu;
use App\Models\JadwalMenu;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CateringHarianController extends Controller
{

    // Fungsi : Menampilkan halaman Manajemen Katering Harian (Card 1 & Card 2).
    // Dijalankan Kapan : Saat admin menekan tombol Manajemen Harian (ikon mata) di tabel katering.
    // Data berasal dari mana : Model Layanan, Menu, dan JadwalMenu.
    // Data dikirim ke mana : View resources/views/admin/catering/harian.blade.php

    public function index(Request $request, string $tipe_layanan = 'harian')
    {
        if ($tipe_layanan !== 'harian') {
            return redirect()->route('admin.dashboard')->with('error', 'Layanan ini bukan tipe Harian.');
        }

        // 1. Mengambil daftar menu milik katering ini (Untuk mengisi form dropdown jadwal dan Card 2)
        $daftarMenu = Menu::where('tipe_layanan', 'harian')->get();

        // 2. Mengambil data jadwal menu yang sudah tersimpan sebelumnya (jika ada)
        $menuIds = $daftarMenu->pluck('id');
        $jadwalTersimpan = JadwalMenu::whereIn('menu_id', $menuIds)->get()->keyBy(function ($item) {
            return $item->tanggal->format('Y-m-d');
        });

        // Mengambil rentang tanggal dari request (jika ada)
        $startDate = $request->input('start_date');
        
        $daftarTanggal = [];
        Carbon::setLocale('id');

        if ($startDate) {
            // Jika ada request generate, set end date ke hari Jumat di minggu tersebut (start_date + 4 hari)
            // Asumsi admin memilih hari Senin. Jika tidak, akan tetap mentok hingga 5 hari ke depan.
            // Lebih baik mencari hari jumat di minggu tersebut.
            $start = Carbon::parse($startDate);
            // Mencari hari Jumat terdekat di minggu yang sama (atau minggu depan jika start_date weekend, dsb).
            // Untuk D3 simple: Jika ini Senin, maka Jumat adalah start + 4 hari. 
            // Kita bisa menggunakan logic: $start->copy()->next(Carbon::FRIDAY) jika ingin strict, 
            // tapi yang paling aman secara visual: admin pilih hari apapun, batas akhirnya adalah hari Jumat terdekat di siklus itu.
            // Atau cukup: $end = $start->copy()->endOfWeek(Carbon::FRIDAY);
            // endOfWeek() bisa dikonfigurasi, tapi defaultnya $start->copy()->next(Carbon::FRIDAY) jika start bukan Jumat.
            // Paling simple dan aman:
            $endDate = $start->copy()->next(Carbon::FRIDAY);
            if ($start->isFriday()) {
                $endDate = $start->copy();
            }

            $period = CarbonPeriod::create($start, $endDate);
            
            foreach ($period as $date) {
                // Jangan buat jadwal untuk Sabtu/Minggu jika tidak sengaja terlewat
                if ($date->isSaturday() || $date->isSunday()) continue;

                $daftarTanggal[] = [
                    'tanggal' => $date->format('Y-m-d'),
                    'hari' => $date->translatedFormat('l')
                ];
            }
        } else {
            // Jika tidak ada request generate, baca tanggal yang sudah ada di database (Sumber Utama)
            $jadwalTersimpanDates = JadwalMenu::whereIn('menu_id', $menuIds)
                                        ->orderBy('tanggal', 'asc')
                                        ->pluck('tanggal')
                                        ->unique();
            
            foreach ($jadwalTersimpanDates as $dateObj) {
                $date = Carbon::parse($dateObj);
                $daftarTanggal[] = [
                    'tanggal' => $date->format('Y-m-d'),
                    'hari' => $date->translatedFormat('l')
                ];
            }
        }

        return view('admin.catering.harian', compact('daftarMenu', 'jadwalTersimpan', 'daftarTanggal', 'startDate', 'tipe_layanan'));
    }

    // =======================================
    // Fungsi : Menyimpan atau memperbarui data Pengaturan Jadwal Menu (Card 1).
    // Dijalankan Kapan : Saat admin menekan tombol "Simpan Jadwal" di bawah tabel jadwal.
    // Data berasal dari mana : Form di halaman harian.blade.php
    // Mengapa ini diperlukan : Untuk mengatur menu apa saja yang tersedia di hari tertentu beserta stok awalnya.
    // =======================================
    public function updateJadwal(Request $request, string $tipe_layanan = 'harian')
    {
        $startDate = $request->input('start_date');
        $jadwalInput = $request->input('jadwal', []);

        if (!$startDate) {
            return back()->with('error', 'Tanggal mulai tidak ditemukan, silakan generate ulang.');
        }

        Carbon::setLocale('id');
        $start = Carbon::parse($startDate);
        $endDate = $start->copy()->next(Carbon::FRIDAY);
        if ($start->isFriday()) {
            $endDate = $start->copy();
        }

        $period = CarbonPeriod::create($start, $endDate);
        
        $daftarTanggal = [];
        foreach ($period as $date) {
            if ($date->isSaturday() || $date->isSunday()) continue;
            $daftarTanggal[] = $date->format('Y-m-d');
        }

        // Melakukan proses validasi tanggal terlebih dahulu sebelum menyimpan data apapun
        foreach ($daftarTanggal as $keyTanggal) {
            $input = $jadwalInput[$keyTanggal] ?? [];
            // Jika hari tersebut dicentang aktif oleh admin
            if (isset($input['aktif']) && $input['aktif'] == '1') {
                $tanggalInput = $input['tanggal'] ?? null;
                $menuId = $input['menu_id'] ?? null;

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
            $menuIds = Menu::where('tipe_layanan', 'harian')->pluck('id');
            $jadwalLama = JadwalMenu::whereIn('menu_id', $menuIds)
                ->where('tanggal', $keyTanggal)
                ->first();

            if ($isAktif) {
                // Jika aktif, kita update (jika ada) atau create (jika belum ada)
                if ($jadwalLama) {
                    // Update data jadwal lama
                    $jadwalLama->update([
                        'menu_id' => $input['menu_id'],
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
                        'menu_id' => $input['menu_id'],
                        'hari' => $namaHari,
                        'tanggal' => $input['tanggal'],
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0,
                        'aktif' => true
                    ]);
                }
                
                // Sinkronisasi menu ke pesanan reschedule (DetailPesanan) yang menu_id-nya null pada tanggal ini
                \App\Models\DetailPesanan::where('tanggal_pengiriman', $input['tanggal'])
                    ->whereNull('menu_id')
                    ->update(['menu_id' => $input['menu_id']]);

                // ExtraHarian was moved to MenuItem, handled independently from JadwalMenu now.
                // So no extra syncing here!
            } else {
                // Jika tidak aktif (tidak dicentang), berarti admin ingin meliburkan/menghapus jadwal hari tersebut
                if ($jadwalLama) {
                    $jadwalLama->delete();
                }
            }
        }

        // Kembali ke halaman sebelumnya dengan parameter pencarian dan pesan sukses
        return redirect()->route('admin.catering.harian', [
            'start_date' => $startDate
        ])->with('success', 'Jadwal berhasil disimpan!');
    }

    public function resetJadwal()
    {
        // Menghapus seluruh data dari tabel jadwal_menus
        JadwalMenu::truncate();

        return redirect()->route('admin.catering.harian')->with('success', 'Semua jadwal berhasil di-reset (dihapus).');
    }
}
