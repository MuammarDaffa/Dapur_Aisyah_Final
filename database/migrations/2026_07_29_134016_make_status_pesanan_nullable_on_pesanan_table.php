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
        Schema::table('pesanan', function (Blueprint $table) {
            // Drop default value and make it nullable
            $table->enum('status_pesanan', ['diproses', 'dibatalkan', 'selesai'])->nullable()->default(null)->change();
        });
        
        // Update existing records that have not been processed or should be null based on business logic.
        // If status_pembayaran is belum_dibayar, the status_pesanan should be null unless it is canceled.
        \Illuminate\Support\Facades\DB::statement("UPDATE pesanan SET status_pesanan = NULL WHERE status_pembayaran = 'belum_dibayar' AND status_pesanan = 'diproses'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE pesanan SET status_pesanan = 'diproses' WHERE status_pesanan IS NULL");

        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('status_pesanan', ['diproses', 'dibatalkan', 'selesai'])->default('diproses')->nullable(false)->change();
        });
    }
};
