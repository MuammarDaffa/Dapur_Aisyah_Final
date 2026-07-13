<?php

namespace Database\Seeders;

use App\Models\CateringPackage;
use App\Models\CateringService;
use App\Models\CustomOption;
use App\Models\Product;
use App\Models\ShippingCost;
use App\Models\District;
use Illuminate\Database\Seeder;

class CateringServiceSeeder extends Seeder
{
    public function run(): void
    {
        // === Katering Harian ===
        $harian = CateringService::updateOrCreate(
            ['slug' => 'katering-harian'],
            [
                'name' => 'Katering Harian',
                'description' => 'Layanan katering harian dengan menu bervariasi setiap hari. Cocok untuk makan siang kantor, keluarga, atau individu yang menginginkan makanan rumahan berkualitas.',
                'serving_types' => ['lunchbox'],
                'min_portion' => 1,
                'base_price' => 25000,
                'order_terms' => 'Pemesanan minimal H-1 sebelum jam 21:00 WIB. Pembatalan maksimal H-1 sebelum jam 21:00 WIB.',
                'schedule_notes' => 'Pengiriman setiap hari Senin - Sabtu, pukul 10:00 - 13:00 WIB.',
                'service_area' => ['Pontianak Barat', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Timur', 'Pontianak Utara'],
                'available_features' => ['daily_menu'],
                'is_active' => true,
                'image' => null,
            ]
        );

        // Produk Katering Harian
        $menuHarian = [
            ['name' => 'Nasi Ayam Geprek', 'price' => 25000, 'description' => 'Nasi putih dengan ayam geprek sambal bawang, lalapan, dan kerupuk.', 'available_days' => ['senin', 'rabu', 'jumat'], 'is_best_seller' => true],
            ['name' => 'Nasi Rendang Sapi', 'price' => 30000, 'description' => 'Nasi putih dengan rendang sapi empuk bumbu rempah, daun singkong rebus.', 'available_days' => ['selasa', 'kamis', 'sabtu'], 'is_best_seller' => true],
            ['name' => 'Nasi Ikan Bakar', 'price' => 28000, 'description' => 'Nasi putih dengan ikan bakar bumbu kecap, sambal matah, dan sayur asem.', 'available_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat'], 'is_best_seller' => false],
            ['name' => 'Nasi Ayam Bakar Madu', 'price' => 27000, 'description' => 'Nasi putih dengan ayam bakar madu, tumis kangkung, dan sambal terasi.', 'available_days' => ['senin', 'rabu', 'jumat', 'sabtu'], 'is_best_seller' => false],
            ['name' => 'Nasi Empal Gentong', 'price' => 32000, 'description' => 'Nasi putih dengan empal gentong khas Cirebon, pelengkap kerupuk.', 'available_days' => ['selasa', 'kamis'], 'is_best_seller' => false],
            ['name' => 'Nasi Gudeg Jogja', 'price' => 26000, 'description' => 'Nasi gudeg Jogja lengkap dengan krecek, telur, dan ayam opor.', 'available_days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'], 'is_best_seller' => true],
        ];

        foreach ($menuHarian as $menu) {
            Product::updateOrCreate(
                ['slug' => \Str::slug($menu['name'])],
                array_merge($menu, [
                    'catering_service_id' => $harian->id,
                    'is_active' => true,
                    'image' => null,
                ])
            );
        }



        // === Katering Acara Kantoran ===
        $kantoran = CateringService::updateOrCreate(
            ['slug' => 'katering-acara-kantoran'],
            [
                'name' => 'Katering Acara Kantoran',
                'description' => 'Layanan katering untuk acara perusahaan seperti meeting, workshop, seminar, atau perayaan kantor. Tersedia paket tetap dan opsi custom sesuai kebutuhan.',
                'serving_types' => ['lunchbox', 'prasmanan', 'plated'],
                'min_portion' => 20,
                'max_portion' => 1000,
                'base_price' => 50000,
                'order_terms' => 'Pemesanan minimal H-3 sebelum tanggal acara. Pembatalan maksimal H-3.',
                'schedule_notes' => 'Tersedia setiap hari, termasuk akhir pekan. Koordinasi waktu pengiriman saat pemesanan.',
                'service_area' => ['Pontianak Barat', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Timur', 'Pontianak Utara'],
                'available_features' => ['packages', 'full_custom'],
                'is_active' => true,
                'image' => null,
            ]
        );

        // Paket Tetap Kantoran
        $packages = [
            ['name' => 'Paket Hemat', 'description' => 'Nasi + 1 Lauk Utama + Sayur + Kerupuk + Air Mineral. Cocok untuk meeting singkat.', 'price' => 35000, 'is_custom' => false],
            ['name' => 'Paket Standard', 'description' => 'Nasi + 1 Lauk Utama + 1 Lauk Pendamping + Sayur + Buah + Minuman. Ideal untuk workshop & seminar.', 'price' => 55000, 'is_custom' => false],
            ['name' => 'Paket Premium', 'description' => 'Nasi + 2 Lauk Utama + 1 Lauk Pendamping + Sayur + Dessert + Buah + Minuman. Untuk acara formal & spesial.', 'price' => 85000, 'is_custom' => false],
        ];

        foreach ($packages as $pkg) {
            CateringPackage::updateOrCreate(
                ['catering_service_id' => $kantoran->id, 'name' => $pkg['name']],
                array_merge($pkg, ['is_active' => true])
            );
        }

        // Custom Options untuk Kantoran
        $customOptions = [
            // Menu
            ['type' => 'menu', 'name' => 'Ayam Goreng Kremes', 'price' => 15000],
            ['type' => 'menu', 'name' => 'Ayam Bakar Madu', 'price' => 18000],
            ['type' => 'menu', 'name' => 'Rendang Sapi', 'price' => 22000],
            ['type' => 'menu', 'name' => 'Ikan Gurame Asam Manis', 'price' => 20000],
            ['type' => 'menu', 'name' => 'Udang Saus Tiram', 'price' => 25000],
            ['type' => 'menu', 'name' => 'Cah Kangkung', 'price' => 8000],
            ['type' => 'menu', 'name' => 'Capcay Goreng', 'price' => 10000],
            // Decoration
            ['type' => 'decoration', 'name' => 'Dekorasi Meja Standar', 'price' => 50000],
            ['type' => 'decoration', 'name' => 'Dekorasi Meja Premium', 'price' => 100000],
            ['type' => 'decoration', 'name' => 'Dekorasi Bunga Meja', 'price' => 75000],
            // Serving Type
            ['type' => 'serving_type', 'name' => 'Nasi Kotak', 'price' => 0],
            ['type' => 'serving_type', 'name' => 'Prasmanan', 'price' => 0],
            // Extra
            ['type' => 'extra', 'name' => 'Paket Minuman (Teh/Kopi)', 'price' => 8000],
            ['type' => 'extra', 'name' => 'Cemilan Assorted', 'price' => 12000],
            ['type' => 'extra', 'name' => 'Buah Potong Segar', 'price' => 10000],
        ];

        foreach ($customOptions as $opt) {
            CustomOption::updateOrCreate(
                ['catering_service_id' => $kantoran->id, 'type' => $opt['type'], 'name' => $opt['name']],
                ['price' => $opt['price'], 'is_active' => true]
            );
        }

        // === Shipping Costs (Default) ===
        $districts = District::all();
        foreach ($districts as $district) {
            ShippingCost::updateOrCreate(
                ['district_id' => $district->id],
                ['cost' => 20000, 'notes' => 'Ongkir default area Pontianak']
            );
        }
    }
}
