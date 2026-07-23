<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalMenu;
use App\Models\ExtraHarian;

class EkstraHarianSeeder extends Seeder
{
    public function run(): void
    {
        $jadwalList = JadwalMenu::all();

        if ($jadwalList->isEmpty()) {
            return;
        }

        $extras = [
            'Sayur Sup',
            'Tahu Cabe Garam',
            'Sambal Teri',
            'Bakwan Udang',
            'Sayur Asam',
            'Telur Dadar',
            'Perkedel Kentang',
        ];

        foreach ($jadwalList as $jadwal) {
            foreach (array_slice($extras, 0, 3) as $extraName) {
                ExtraHarian::firstOrCreate([
                    'jadwal_menu_id' => $jadwal->id,
                    'nama' => $extraName,
                ], [
                    'harga' => 2000,
                ]);
            }
        }
    }
}
