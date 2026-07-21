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
        // 1. Remove foreign keys and columns from pesanan
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['desa_id']);
            $table->dropColumn(['kecamatan_id', 'desa_id']);
        });

        // 2. Drop the desa table
        Schema::dropIfExists('desa');

        // 3. Drop the kecamatan table
        Schema::dropIfExists('kecamatan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->foreignId('desa_id')->nullable()->constrained('desa')->nullOnDelete();
        });
    }
};
