<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->foreignId('layanan_katering_id')->nullable()->after('user_id')
                  ->constrained('layanan_katering')->nullOnDelete();
            $table->unsignedBigInteger('serving_type_id')->nullable()->after('item_type');
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign(['layanan_katering_id']);
            $table->dropColumn(['layanan_katering_id', 'serving_type_id']);
        });
    }
};
