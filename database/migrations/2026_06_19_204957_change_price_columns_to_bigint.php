<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->decimal('base_price', 15, 2)->default(0)->change();
        });
        Schema::table('produk', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->change();
        });
        Schema::table('paket_katering', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->change();
        });
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->default(0)->change();
        });
        Schema::table('ongkos_kirim', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->default(20000)->change();
        });
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->default(0)->change();
            $table->decimal('ongkos_kirim', 15, 2)->default(0)->change();
            $table->decimal('subtotal', 15, 2)->default(0)->change();
        });
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->decimal('base_price', 12, 2)->default(0)->change();
        });
        Schema::table('produk', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->change();
        });
        Schema::table('paket_katering', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->change();
        });
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->decimal('harga', 10, 2)->default(0)->change();
        });
        Schema::table('ongkos_kirim', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->default(20000)->change();
        });
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->default(0)->change();
            $table->decimal('ongkos_kirim', 10, 2)->default(0)->change();
            $table->decimal('subtotal', 12, 2)->default(0)->change();
        });
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('subtotal', 12, 2)->change();
        });
    }
};
