<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_harian_extra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_harian_id')->constrained('menu_harian')->cascadeOnDelete();
            $table->string('nama_extra');
            $table->bigInteger('harga');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_harian_extra');
    }
};