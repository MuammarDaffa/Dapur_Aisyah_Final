<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->dropColumn('produk_id');
            $table->foreignId('menu_harian_id')->nullable()->after('cart_group_id')->constrained('menu_harian')->nullOnDelete();
        });
        
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->dropColumn('produk_id');
            $table->foreignId('menu_harian_id')->nullable()->after('pesanan_id')->constrained('menu_harian')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign(['menu_harian_id']);
            $table->dropColumn('menu_harian_id');
            // Can't reliably recreate produk_id without the table
        });
        
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['menu_harian_id']);
            $table->dropColumn('menu_harian_id');
        });
    }
};