<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_service_id')->constrained('catering_services')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
            $table->index('catering_service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_periods');
    }
};
