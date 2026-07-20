<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->unsignedInteger('minimal_order_days')->nullable()->after('schedule_notes');
            $table->time('batas_waktu_pemesanan')->nullable()->after('minimal_order_days');
        });

        // Set default cutoff berdasarkan tipe layanan yang sudah ada
        // Daily: 1 hari, jam 21:00 (sesuai perilaku hardcoded sebelumnya)
        // Event: 3 hari, tanpa cutoff jam
        $services = \DB::table('layanan_katering')->get();
        foreach ($services as $service) {
            $features = json_decode($service->fitur_tersedia, true) ?? [];
            if (in_array('daily_menu', $features)) {
                \DB::table('layanan_katering')->where('id', $service->id)->update([
                    'minimal_order_days' => 1,
                    'batas_waktu_pemesanan' => '21:00',
                ]);
            } elseif (in_array('packages', $features) || in_array('full_custom', $features)) {
                \DB::table('layanan_katering')->where('id', $service->id)->update([
                    'minimal_order_days' => 3,
                    'batas_waktu_pemesanan' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->dropColumn(['minimal_order_days', 'batas_waktu_pemesanan']);
        });
    }
};
