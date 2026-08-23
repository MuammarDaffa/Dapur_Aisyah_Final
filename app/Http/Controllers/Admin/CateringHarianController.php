<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\JadwalMenu;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CateringHarianController extends Controller
{
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

        // generate sampai hari jumat
        if ($startDate) {
            $start = Carbon::parse($startDate);
            $today = Carbon::now('Asia/Jakarta')->startOfDay();
            
            if ($start->lt($today)) {
                return redirect()->route('admin.catering.harian')->with('swal_error', 'Tanggal mulai tidak boleh sebelum tanggal hari ini.');
            }

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

    public function updateJadwal(Request $request, string $tipe_layanan = 'harian')
    {
        $startDate = $request->input('start_date');
        
        if ($startDate) {
            $start = Carbon::parse($startDate);
            $today = Carbon::now('Asia/Jakarta')->startOfDay();
            
            if ($start->lt($today)) {
                return redirect()->route('admin.catering.harian')->with('swal_error', 'Tanggal mulai tidak boleh sebelum tanggal hari ini.');
            }
        }

        $jadwalInput = $request->input('jadwal', []);
        $daftarTanggal = array_keys($jadwalInput);

        Carbon::setLocale('id');

        // Melakukan proses validasi tanggal terlebih dahulu sebelum menyimpan data apapun
        foreach ($daftarTanggal as $keyTanggal) {
            $input = $jadwalInput[$keyTanggal] ?? [];
            $menuId = $input['menu_id'] ?? null;

            // Jika admin memilih menu dari dropdown
            if (!empty($menuId)) {
                $tanggalInput = $input['tanggal'] ?? null;

                // Memastikan input tanggal sesuai dengan barisnya
                if ($keyTanggal !== $tanggalInput) {
                    return back()->with('error', "Validasi Gagal: Tanggal yang dipilih tidak sesuai.");
                }
            }
        }

        // Jika lolos validasi, kita mulai proses penyimpanan (menyimpan/mengubah/menghapus)
        foreach ($daftarTanggal as $keyTanggal) {
            $input = $jadwalInput[$keyTanggal] ?? [];
            $menuId = $input['menu_id'] ?? null;
            $isAktif = !empty($menuId);
            
            $namaHari = Carbon::parse($keyTanggal)->translatedFormat('l');

            // Coba cari apakah sebelumnya jadwal hari ini sudah pernah dibuat
            $menuIds = Menu::where('tipe_layanan', 'harian')->pluck('id');
            $jadwalLama = JadwalMenu::whereIn('menu_id', $menuIds)
                ->where('tanggal', $keyTanggal)
                ->first();

            if ($isAktif) {
                // Jika aktif, kita update (jika ada) atau create (jika belum ada)
                if ($jadwalLama) {
                    $stokAwalBaru = (int) ($input['stok_awal'] ?? 0);

                    if ($jadwalLama->menu_id == $input['menu_id']) {
                        // Jika menu masih sama, pertahankan jumlah terjual
                        // Terjual = stok awal lama - sisa stok lama
                        $terjual = $jadwalLama->stok_awal - $jadwalLama->stok_tersisa;
                        
                        // Hitung sisa stok baru = stok awal baru - terjual
                        $sisaStokBaru = $stokAwalBaru - $terjual;

                        $jadwalLama->update([
                            'stok_awal' => $stokAwalBaru,
                            'stok_tersisa' => $sisaStokBaru
                        ]);
                    } else {
                        // Jika menu berubah, reset terjual menjadi 0 (sisa stok = stok awal)
                        $jadwalLama->update([
                            'menu_id' => $input['menu_id'],
                            'tanggal' => $input['tanggal'],
                            'hari' => $namaHari,
                            'stok_awal' => $stokAwalBaru,
                            'stok_tersisa' => $stokAwalBaru
                        ]);
                    }
                    $activeJadwal = $jadwalLama;
                } else {
                    // Create data jadwal baru
                    $activeJadwal = JadwalMenu::create([
                        'menu_id' => $input['menu_id'],
                        'hari' => $namaHari,
                        'tanggal' => $input['tanggal'],
                        'stok_awal' => $input['stok_awal'] ?? 0,
                        'stok_tersisa' => $input['stok_awal'] ?? 0
                    ]);
                }
                
                // mengisi tanggal pengiriman di detail pesanan yang menu_id nya null
                \App\Models\DetailPesanan::where('tanggal_pengiriman', $input['tanggal'])
                    ->whereNull('menu_id')
                    ->update(['menu_id' => $input['menu_id']]);

            } else {
                if ($jadwalLama) {
                    $jadwalLama->delete();
                }
            }
        }

        $redirectParams = $startDate ? ['start_date' => $startDate] : [];
        return redirect()->route('admin.catering.harian', $redirectParams)->with('swal_success', 'Jadwal tersimpan');
    }

    public function resetJadwal()
    {
        // Menghapus seluruh data dari tabel jadwal_menus
        JadwalMenu::truncate();

        return redirect()->route('admin.catering.harian');
    }
}
