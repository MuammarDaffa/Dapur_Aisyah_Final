<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_periode_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_menu_id')->constrained('periode_menu')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->date('menu_date');
            $table->timestamps();

            // 1 tanggal = 1 produk per periode
            $table->unique(['periode_menu_id', 'menu_date'], 'period_date_unique');
            $table->index('menu_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_periode_menu');
    }
};
