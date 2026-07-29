<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new columns
        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['belum_dibayar', 'dp', 'lunas'])->default('belum_dibayar')->after('status');
            $table->enum('status_pesanan', ['diproses', 'dibatalkan', 'selesai'])->default('diproses')->after('status_pembayaran');
        });

        // 2. Migrate existing data
        DB::statement("UPDATE pesanan SET status_pembayaran = 'belum_dibayar', status_pesanan = 'diproses' WHERE status = 'belum_bayar'");
        DB::statement("UPDATE pesanan SET status_pembayaran = 'dp', status_pesanan = 'diproses' WHERE status = 'dp'");
        DB::statement("UPDATE pesanan SET status_pembayaran = 'lunas', status_pesanan = 'diproses' WHERE status = 'lunas'");
        DB::statement("UPDATE pesanan SET status_pembayaran = 'belum_dibayar', status_pesanan = 'dibatalkan' WHERE status = 'dibatalkan'");

        // 3. Drop the old column
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('status', ['belum_bayar', 'dp', 'lunas', 'dibatalkan'])->default('belum_bayar');
        });

        DB::statement("UPDATE pesanan SET status = 'belum_bayar' WHERE status_pembayaran = 'belum_dibayar' AND status_pesanan != 'dibatalkan'");
        DB::statement("UPDATE pesanan SET status = 'dp' WHERE status_pembayaran = 'dp'");
        DB::statement("UPDATE pesanan SET status = 'lunas' WHERE status_pembayaran = 'lunas'");
        DB::statement("UPDATE pesanan SET status = 'dibatalkan' WHERE status_pesanan = 'dibatalkan'");

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
            $table->dropColumn('status_pesanan');
        });
    }
};
