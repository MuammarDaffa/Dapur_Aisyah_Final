<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->json('fitur_tersedia')->nullable()->after('service_area');
        });
    }

    public function down(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->dropColumn('fitur_tersedia');
        });
    }
};
