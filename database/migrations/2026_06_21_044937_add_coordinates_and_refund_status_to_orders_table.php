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
            $table->decimal('latitude', 10, 8)->nullable()->after('detail_alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('refund_status', 20)->default('none')->after('status_pembayaran');
            // none | pending | refunded
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'refund_status']);
        });
    }
};
