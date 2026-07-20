<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_katering', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('deskripsi');
            $table->json('serving_types')->nullable(); // ['lunchbox','prasmanan','plated']
            $table->integer('min_portion')->default(1);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->text('order_terms')->nullable();
            $table->text('schedule_notes')->nullable();
            $table->json('service_area')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_katering');
    }
};
