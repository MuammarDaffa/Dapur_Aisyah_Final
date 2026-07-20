<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_katering_id')->constrained('layanan_katering')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('image', 255)->nullable();
            $table->boolean('is_best_seller')->default(false);
            $table->json('available_days')->nullable(); // ['senin','selasa',...]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
