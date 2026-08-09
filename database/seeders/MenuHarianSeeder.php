<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = \App\Models\Layanan::where('tipe', 'harian')->first();

        if (!$layanan) {
            $this->command->error('Layanan Katering Harian tidak ditemukan. Pastikan seeder Layanan sudah dijalankan.');
            return;
        }

        $dataMenus = [
            [
                'nama_menu' => 'Semur Daging',
                'items' => ['Sayur Sup', 'Tahu Cabe Garam', 'Sambal']
            ],
            [
                'nama_menu' => 'Ayam Goreng Lengkuas',
                'items' => ['Gulai Daun Ubi', 'Bakwan udang', 'sambal']
            ],
            [
                'nama_menu' => 'Udang Goreng',
                'items' => ['Sayur Asam', 'Tempe Goreng', 'Ikan Asin Balado']
            ],
            [
                'nama_menu' => 'Ayam Goreng Mentega',
                'items' => ['Sup Labu Kuning', 'Telur Dadar', 'Sambal Kentang Hati']
            ],
            [
                'nama_menu' => 'Ikan Goreng',
                'items' => ['Tumis Kacang Panjang', 'Gulai Tahu', 'Paru Balado']
            ],
            [
                'nama_menu' => 'Sapi Lada Hitam',
                'items' => ['Sup Kimlo', 'Bakwan Udang', 'Sambal']
            ],
            [
                'nama_menu' => 'Ayam Fillet',
                'items' => ['Soto Ayam', 'Tahu Goreng Tepung', 'Sambal']
            ],
            [
                'nama_menu' => 'Udang Tahu Jahe',
                'items' => ['Cah Sayur', 'Tempe Goreng', 'Terong Balado']
            ],
            [
                'nama_menu' => 'Ayam Goreng Cabe Hijau',
                'items' => ['Tongseng Ayam', 'Kecap Tahu Telur', 'Sambal']
            ],
        ];

        \Illuminate\Support\Facades\DB::transaction(function () use ($layanan, $dataMenus) {
            foreach ($dataMenus as $menuData) {
                // Buat atau update Menu Harian
                $menu = \App\Models\Menu::firstOrCreate(
                    [
                        'layanan_id' => $layanan->id,
                        'nama_menu' => $menuData['nama_menu'],
                    ],
                    [
                        'deskripsi' => 'Menu Harian: ' . $menuData['nama_menu'],
                        'harga' => 30000,
                        'status' => 1,
                    ]
                );

                // Buat atau update Item Menu
                foreach ($menuData['items'] as $itemName) {
                    \App\Models\TambahanLaukPauk::firstOrCreate(
                        [
                            'menu_id' => $menu->id,
                            'nama' => $itemName,
                        ],
                        [
                            'harga' => 5000,
                        ]
                    );
                }
            }
        });

        $this->command->info('Menu Harian dan Item Menu berhasil di-seed.');
    }
}
