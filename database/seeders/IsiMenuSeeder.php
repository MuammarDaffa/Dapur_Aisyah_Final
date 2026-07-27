<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IsiMenu;

class IsiMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'Sapi Lada Hitam',
            'Sup Jamur',
            'Ayam Fillet Asam Manis',
            'Sambal Kentang Hati',
            'Acar Mentimun',
            'Sambal',
            'Kerupuk',
            'Rendang Daging',
            'Sup Kimlo',
            'Ayam Goreng Lengkuas',
            'Mie Goreng Jawa',
            'Daging Semur',
            'Cap Cay',
            'Kentang Mustofa',
        ];

        // Ambil ID Menu Acara pertama yang ada di database, jika tidak ada fallback ke 1
        $menuAcara = \App\Models\MenuAcara::first();
        $menuAcaraId = $menuAcara ? $menuAcara->id : 1;

        foreach ($menus as $menu) {
            IsiMenu::updateOrCreate(
                [
                    'menu_acara_id' => $menuAcaraId,
                    'nama' => $menu,
                ],
                [
                    'harga' => 2000,
                ]
            );
        }
    }
}
