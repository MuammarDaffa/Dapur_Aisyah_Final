<?php

namespace Database\Seeders;

use App\Models\PaketKatering;
use App\Models\LayananKatering;
use App\Models\OpsiKustom;
use App\Models\Produk;
use App\Models\OngkosKirim;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class LayananKateringSeeder extends Seeder
{
    public function run(): void
    {
        // === Katering Harian ===
        $harian = LayananKatering::updateOrCreate(
            ['slug' => 'katering-harian'],
            [
                'name' => 'Katering Harian',
                'deskripsi' => 'Layanan katering harian dengan menu bervariasi setiap hari. Cocok untuk makan siang kantor, keluarga, atau individu yang menginginkan makanan rumahan berkualitas.',
                'serving_types' => ['lunchbox'],
                'min_portion' => 1,
                'base_price' => 25000,
                'order_terms' => 'Pemesanan minimal H-1 sebelum jam 21:00 WIB. Pembatalan maksimal H-1 sebelum jam 21:00 WIB.',
                'schedule_notes' => 'Pengiriman setiap hari Senin - Sabtu, pukul 10:00 - 13:00 WIB.',
                'service_area' => ['Pontianak Barat', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Timur', 'Pontianak Utara'],
                'fitur_tersedia' => ['daily_menu'],
                'is_active' => true,
                'image' => null,
            ]
        );

        // Produk Katering Harian
        $menuHarian = [
            ['name' => 'Nasi Ayam Geprek', 'harga' => 25000, 'deskripsi' => 'Nasi putih dengan ayam geprek sambal bawang, lalapan, dan kerupuk.', 'is_best_seller' => true],
            ['name' => 'Nasi Rendang Sapi', 'harga' => 30000, 'deskripsi' => 'Nasi putih dengan rendang sapi empuk bumbu rempah, daun singkong rebus.', 'is_best_seller' => true],
            ['name' => 'Nasi Ikan Bakar', 'harga' => 28000, 'deskripsi' => 'Nasi putih dengan ikan bakar bumbu kecap, sambal matah, dan sayur asem.', 'is_best_seller' => false],
            ['name' => 'Nasi Ayam Bakar Madu', 'harga' => 27000, 'deskripsi' => 'Nasi putih dengan ayam bakar madu, tumis kangkung, dan sambal terasi.', 'is_best_seller' => false],
            ['name' => 'Nasi Empal Gentong', 'harga' => 32000, 'deskripsi' => 'Nasi putih dengan empal gentong khas Cirebon, pelengkap kerupuk.', 'is_best_seller' => false],
            ['name' => 'Nasi Gudeg Jogja', 'harga' => 26000, 'deskripsi' => 'Nasi gudeg Jogja lengkap dengan krecek, telur, dan ayam opor.', 'is_best_seller' => true],
        ];

        foreach ($menuHarian as $menu) {
            Produk::updateOrCreate(
                ['slug' => \Str::slug($menu['name'])],
                array_merge($menu, [
                    'layanan_katering_id' => $harian->id,
                    'is_active' => true,
                    'image' => null,
                ])
            );
        }



        // === Katering Acara Kantoran ===
        $kantoran = LayananKatering::updateOrCreate(
            ['slug' => 'katering-acara-kantoran'],
            [
                'name' => 'Katering Acara Kantoran',
                'deskripsi' => 'Layanan katering untuk acara perusahaan seperti meeting, workshop, seminar, atau perayaan kantor. Tersedia paket tetap dan opsi custom sesuai kebutuhan.',
                'serving_types' => ['lunchbox', 'prasmanan', 'plated'],
                'min_portion' => 20,
                'maksimal_porsi' => 1000,
                'base_price' => 50000,
                'order_terms' => 'Pemesanan minimal H-3 sebelum tanggal acara. Pembatalan maksimal H-3.',
                'schedule_notes' => 'Tersedia setiap hari, termasuk akhir pekan. Koordinasi waktu pengiriman saat pemesanan.',
                'service_area' => ['Pontianak Barat', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Timur', 'Pontianak Utara'],
                'fitur_tersedia' => ['packages', 'full_custom'],
                'is_active' => true,
                'image' => null,
            ]
        );

        // Paket Tetap Kantoran
        $packages = [
            ['name' => 'Paket Hemat', 'deskripsi' => 'Nasi + 1 Lauk Utama + Sayur + Kerupuk + Air Mineral. Cocok untuk meeting singkat.', 'harga' => 35000, 'is_custom' => false],
            ['name' => 'Paket Standard', 'deskripsi' => 'Nasi + 1 Lauk Utama + 1 Lauk Pendamping + Sayur + Buah + Minuman. Ideal untuk workshop & seminar.', 'harga' => 55000, 'is_custom' => false],
            ['name' => 'Paket Premium', 'deskripsi' => 'Nasi + 2 Lauk Utama + 1 Lauk Pendamping + Sayur + Dessert + Buah + Minuman. Untuk acara formal & spesial.', 'harga' => 85000, 'is_custom' => false],
        ];

        foreach ($packages as $pkg) {
            PaketKatering::updateOrCreate(
                ['layanan_katering_id' => $kantoran->id, 'name' => $pkg['name']],
                array_merge($pkg, ['is_active' => true])
            );
        }

        // Custom Options untuk Kantoran
        $opsiKustom = [
            // Menu
            ['type' => 'menu', 'name' => 'Ayam Goreng Kremes', 'harga' => 15000],
            ['type' => 'menu', 'name' => 'Ayam Bakar Madu', 'harga' => 18000],
            ['type' => 'menu', 'name' => 'Rendang Sapi', 'harga' => 22000],
            ['type' => 'menu', 'name' => 'Ikan Gurame Asam Manis', 'harga' => 20000],
            ['type' => 'menu', 'name' => 'Udang Saus Tiram', 'harga' => 25000],
            ['type' => 'menu', 'name' => 'Cah Kangkung', 'harga' => 8000],
            ['type' => 'menu', 'name' => 'Capcay Goreng', 'harga' => 10000],
            // Decoration
            ['type' => 'decoration', 'name' => 'Dekorasi Meja Standar', 'harga' => 50000],
            ['type' => 'decoration', 'name' => 'Dekorasi Meja Premium', 'harga' => 100000],
            ['type' => 'decoration', 'name' => 'Dekorasi Bunga Meja', 'harga' => 75000],
            // Serving Type
            ['type' => 'tipe_penyajian', 'name' => 'Nasi Kotak', 'harga' => 0],
            ['type' => 'tipe_penyajian', 'name' => 'Prasmanan', 'harga' => 0],
            // Extra
            ['type' => 'extra', 'name' => 'Paket Minuman (Teh/Kopi)', 'harga' => 8000],
            ['type' => 'extra', 'name' => 'Cemilan Assorted', 'harga' => 12000],
            ['type' => 'extra', 'name' => 'Buah Potong Segar', 'harga' => 10000],
        ];

        foreach ($opsiKustom as $opt) {
            OpsiKustom::updateOrCreate(
                ['layanan_katering_id' => $kantoran->id, 'type' => $opt['type'], 'name' => $opt['name']],
                ['harga' => $opt['harga'], 'is_active' => true]
            );
        }

        // === Shipping Costs (Default) ===
        $kecamatan = Kecamatan::all();
        foreach ($kecamatan as $kecamatan) {
            OngkosKirim::updateOrCreate(
                ['kecamatan_id' => $kecamatan->id],
                ['cost' => 20000, 'catatan' => 'Ongkir default area Pontianak']
            );
        }
    }
}
