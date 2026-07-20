<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });

        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
    }
};
