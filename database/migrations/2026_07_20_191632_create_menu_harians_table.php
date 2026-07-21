<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_katering_id')->constrained('layanan_katering')->cascadeOnDelete();
            $table->string('hari');
            $table->date('tanggal')->nullable();
            $table->string('nama_menu')->nullable();
            $table->bigInteger('harga')->nullable();
            $table->integer('stok_awal')->nullable();
            $table->integer('stok_tersisa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_harian');
    }
};