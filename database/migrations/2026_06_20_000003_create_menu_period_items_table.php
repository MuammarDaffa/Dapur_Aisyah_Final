<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_period_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_period_id')->constrained('menu_periods')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->date('menu_date');
            $table->timestamps();

            // 1 tanggal = 1 produk per periode
            $table->unique(['menu_period_id', 'menu_date'], 'period_date_unique');
            $table->index('menu_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_period_items');
    }
};
