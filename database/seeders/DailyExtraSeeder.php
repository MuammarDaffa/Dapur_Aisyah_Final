<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomOption;
use App\Models\CateringService;

class DailyExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari layanan Catering Daily
        $dailyService = CateringService::whereJsonContains('available_features', 'daily_menu')->first();

        if (!$dailyService) {
            $this->command->warn("Layanan Catering Daily tidak ditemukan. Seeder dibatalkan.");
            return;
        }

        // Daftar Extra
        $extras = [
            'Sayur Sup',
            'Tahu Cabe Garam',
            'Sambal',
            'Gulai Daun Ubi',
            'Bakwan Udang',
            'Sayur Asam',
            'Ikan Asin Balado',
            'Sup Labu Kuning',
            'Telur Dadar',
            'Sambal Kentang Hati',
            'Tumis Kacang Panjang',
            'Gulai Tahu',
            'Paru Balado',
            'Ampela Balado',
            'Bakwan Jagung',
            'Perkedel Kentang',
            'Tempe Orak-Arik',
            'Sambal Teri Kecombrang',
        ];

        foreach ($extras as $extraName) {
            CustomOption::firstOrCreate(
                [
                    'catering_service_id' => $dailyService->id,
                    'type' => 'extra',
                    'name' => $extraName,
                ],
                [
                    'price' => 2000,
                    'min_qty' => 1,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info("Seeder selesai. " . count($extras) . " data Extra berhasil diproses untuk Catering Harian.");
    }
}
