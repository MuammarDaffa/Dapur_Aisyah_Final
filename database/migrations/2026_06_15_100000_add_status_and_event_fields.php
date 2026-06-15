<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Task 11: Tambah status ketersediaan produk
        Schema::table('products', function (Blueprint $table) {
            $table->enum('status', ['tersedia', 'habis'])->default('tersedia')->after('is_active');
        });

        // Task 10: Tambah jam mulai acara pada orders
        Schema::table('orders', function (Blueprint $table) {
            $table->time('event_start_time')->nullable()->after('order_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('event_start_time');
        });
    }
};
