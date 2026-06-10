<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_service_id')->constrained('catering_services')->cascadeOnDelete();
            $table->enum('type', ['menu', 'decoration', 'serving_type', 'extra']);
            $table->string('name', 150);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_options');
    }
};
