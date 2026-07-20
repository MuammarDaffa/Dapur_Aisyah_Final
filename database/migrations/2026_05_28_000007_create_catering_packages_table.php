<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_katering', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_katering_id')->constrained('layanan_katering')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->boolean('is_custom')->default(false); // 0=paket tetap, 1=custom
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_katering');
    }
};
