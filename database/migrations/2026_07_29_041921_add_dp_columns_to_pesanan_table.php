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
            // Kita tambahkan 2 kolom baru tepat setelah kolom 'total'
            $table->integer('jumlah_dp')->default(0)->after('total');
            $table->integer('sisa_pembayaran')->default(0)->after('jumlah_dp');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dp', 'sisa_pembayaran']);
        });
    }

};
