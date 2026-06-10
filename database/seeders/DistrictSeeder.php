<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
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

        foreach ($data as $districtName => $villages) {
            $district = District::updateOrCreate(['name' => $districtName]);

            foreach ($villages as $villageName) {
                Village::updateOrCreate(
                    ['district_id' => $district->id, 'name' => $villageName]
                );
            }
        }
    }
}
