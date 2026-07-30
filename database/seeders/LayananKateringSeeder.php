<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\MenuHarian;
use App\Models\JadwalMenu;
use App\Models\ExtraHarian;
use App\Models\MenuAcara;
use App\Models\ExtraAcara;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LayananKateringSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Layanan Harian
        $harianA = Layanan::create([
            'nama' => 'Katering Harian A',
            'tipe' => 'harian',
            'status' => true,
        ]);

        $harianB = Layanan::create([
            'nama' => 'Katering Harian B',
            'tipe' => 'harian',
            'status' => true,
        ]);

        // 2. Layanan Acara
        $wedding = Layanan::create([
            'nama' => 'Wedding',
            'tipe' => 'acara',
            'status' => true,
        ]);

        $kantoran = Layanan::create([
            'nama' => 'Kantoran',
            'tipe' => 'acara',
            'status' => true,
        ]);

        $seminar = Layanan::create([
            'nama' => 'Seminar',
            'tipe' => 'acara',
            'status' => true,
        ]);

        $gathering = Layanan::create([
            'nama' => 'Gathering',
            'tipe' => 'acara',
            'status' => true,
        ]);

        // 3. Menu Harian & Jadwal
        $menusHarianData = [
            ['nama_menu' => 'Nasi Ayam Geprek', 'harga' => 25000, 'deskripsi' => 'Nasi putih dengan ayam geprek sambal bawang dan lalapan.'],
            ['nama_menu' => 'Nasi Rendang Sapi', 'harga' => 30000, 'deskripsi' => 'Nasi putih dengan rendang sapi empuk bumbu rempah.'],
            ['nama_menu' => 'Nasi Ikan Bakar', 'harga' => 28000, 'deskripsi' => 'Nasi putih dengan ikan bakar bumbu kecap.'],
            ['nama_menu' => 'Nasi Ayam Bakar Madu', 'harga' => 27000, 'deskripsi' => 'Nasi putih dengan ayam bakar madu.'],
            ['nama_menu' => 'Nasi Gudeg Jogja', 'harga' => 26000, 'deskripsi' => 'Nasi gudeg Jogja lengkap dengan krecek dan telur.'],
        ];

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $startDate = Carbon::now('Asia/Jakarta')->startOfWeek();

        foreach ($menusHarianData as $menuIndex => $mdata) {
            $menuHarian = MenuHarian::create([
                'layanan_id' => $harianA->id,
                'nama_menu' => $mdata['nama_menu'],
                'deskripsi' => $mdata['deskripsi'],
                'harga' => $mdata['harga'],
                'status' => true,
            ]);

            // Buat Jadwal Menu (Senin - Jumat)
            foreach ($hariList as $hIndex => $hari) {
                $tanggalObj = $startDate->copy()->addDays($hIndex);
                $jadwal = JadwalMenu::create([
                    'menu_harian_id' => $menuHarian->id,
                    'hari' => $hari,
                    'aktif' => true,
                    'tanggal' => $tanggalObj->format('Y-m-d'),
                    'stok_awal' => 50,
                    'stok_tersisa' => 50,
                ]);

                // Extra Harian per Jadwal
                ExtraHarian::create([
                    'jadwal_menu_id' => $jadwal->id,
                    'nama' => 'Sayur Sup',
                    'harga' => 2000,
                ]);
                ExtraHarian::create([
                    'jadwal_menu_id' => $jadwal->id,
                    'nama' => 'Tahu Cabe Garam',
                    'harga' => 2000,
                ]);
                ExtraHarian::create([
                    'jadwal_menu_id' => $jadwal->id,
                    'nama' => 'Sambal Ekstra',
                    'harga' => 1000,
                ]);
            }
        }

        // 4. Menu Acara & Extra Acara
        $menusAcaraKantoran = [
            ['nama_menu' => 'Menu Meeting Standard', 'harga_per_porsi' => 35000, 'deskripsi' => 'Nasi + Lauk Utama + Sayur + Kerupuk + Air Mineral.'],
            ['nama_menu' => 'Menu Seminar Premium', 'harga_per_porsi' => 55000, 'deskripsi' => 'Nasi + 2 Lauk + Sayur + Buah + Minuman.'],
            ['nama_menu' => 'Menu Executive Gathering', 'harga_per_porsi' => 85000, 'deskripsi' => 'Nasi + 3 Lauk + Dessert + Fruit Punch.'],
        ];

        foreach ($menusAcaraKantoran as $acData) {
            $menuAcara = MenuAcara::create([
                'layanan_id' => $kantoran->id,
                'nama_menu' => $acData['nama_menu'],
                'harga_per_porsi' => $acData['harga_per_porsi'],
                'deskripsi' => $acData['deskripsi'],
                'status' => true,
            ]);

            ExtraAcara::create([
                'menu_acara_id' => $menuAcara->id,
                'nama' => 'Paket Minuman Teh & Kopi',
                'harga' => 8000,
            ]);
            ExtraAcara::create([
                'menu_acara_id' => $menuAcara->id,
                'nama' => 'Cemilan Assorted',
                'harga' => 12000,
            ]);
            ExtraAcara::create([
                'menu_acara_id' => $menuAcara->id,
                'nama' => 'Buah Potong Segar',
                'harga' => 10000,
            ]);
        }

        // Menu Wedding
        $menuWedding = MenuAcara::create([
            'layanan_id' => $wedding->id,
            'nama_menu' => 'Buffet Royal Wedding',
            'harga_per_porsi' => 120000,
            'deskripsi' => 'Prasmanan Mewah 5 Lauk Utama, Stall Es Krim & Buah.',
            'status' => true,
        ]);

        ExtraAcara::create([
            'menu_acara_id' => $menuWedding->id,
            'nama' => 'Stall Kambing Guling',
            'harga' => 25000,
        ]);
        ExtraAcara::create([
            'menu_acara_id' => $menuWedding->id,
            'nama' => 'Stall Zuppa Soup',
            'harga' => 18000,
        ]);
    }
}
