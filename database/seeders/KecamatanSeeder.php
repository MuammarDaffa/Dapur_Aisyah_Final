<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Pontianak Barat' => [
                'Pal Lima', 'Sungai Beliung', 'Sungai Jawi Dalam', 'Sungai Jawi Luar',
            ],
            'Pontianak Kota' => [
                'Darat Sekip', 'Mariana', 'Sungai Bangkong', 'Sungai Jawi',
            ],
            'Pontianak Selatan' => [
                'Akcaya', 'Benua Melayu Darat', 'Benua Melayu Laut', 'Kota Baru', 'Parit Tokaya',
            ],
            'Pontianak Tenggara' => [
                'Bangka Belitung Darat', 'Bangka Belitung Laut', 'Bansir Darat', 'Bansir Laut',
            ],
            'Pontianak Timur' => [
                'Banjar Serasan', 'Dalam Bugis', 'Parit Mayor', 'Saigon',
                'Tambelan Sampit', 'Tanjung Hulu', 'Tanjung Hilir',
            ],
            'Pontianak Utara' => [
                'Batu Layang', 'Siantan Hilir', 'Siantan Hulu', 'Siantan Tengah',
            ],
        ];

        foreach ($data as $districtName => $desa) {
            $kecamatan = Kecamatan::updateOrCreate(['name' => $districtName]);

            foreach ($desa as $villageName) {
                Desa::updateOrCreate(
                    ['kecamatan_id' => $kecamatan->id, 'name' => $villageName]
                );
            }
        }
    }
}
