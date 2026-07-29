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
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->date('tanggal_pengiriman')->nullable()->after('menu_id');
            $table->boolean('is_rescheduled')->default(false)->after('tanggal_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pengiriman', 'is_rescheduled']);
        });
    }
};
