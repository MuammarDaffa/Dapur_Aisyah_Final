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
        Schema::table('layanan', function (Blueprint $table) {
            $table->renameColumn('kapasitas_total', 'kapasitas_porsi_per_minggu');
            $table->dropColumn(['kapasitas_tersisa', 'minimal_porsi']);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'status_pembayaran']);
            if (Schema::hasColumn('pesanan', 'status_pesanan')) {
                $table->dropColumn('status_pesanan');
            }
        });
        
        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('status_new', ['belum_bayar', 'dp', 'lunas', 'dibatalkan'])->default('belum_bayar');
        });
        
        DB::statement("UPDATE pesanan SET status_new = 'belum_bayar' WHERE status IN ('menunggu_pembayaran', 'pending')");
        DB::statement("UPDATE pesanan SET status_new = 'lunas' WHERE status IN ('selesai', 'sudah_dibayar', 'lunas')");
        DB::statement("UPDATE pesanan SET status_new = 'dibatalkan' WHERE status = 'dibatalkan'");
        
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('pesanan', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->renameColumn('kapasitas_porsi_per_minggu', 'kapasitas_total');
            $table->integer('kapasitas_tersisa')->nullable();
            $table->integer('minimal_porsi')->nullable();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_pembayaran')->default('pending');
            $table->string('status')->default('pending');
        });
    }
};
