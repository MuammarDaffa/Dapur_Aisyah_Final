<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Task 11: Tambah status ketersediaan produk
        Schema::table('produk', function (Blueprint $table) {
            $table->enum('status', ['tersedia', 'habis'])->default('tersedia')->after('is_active');
        });

        // Task 10: Tambah jam mulai acara pada pesanan
        Schema::table('pesanan', function (Blueprint $table) {
            $table->time('event_start_time')->nullable()->after('tanggal_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('event_start_time');
        });
    }
};
