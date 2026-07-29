<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->integer('porsi');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('detail_pesanan_menu_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_pesanan_id')->constrained('detail_pesanan')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_item')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropColumn('menu_id');
            $table->dropColumn('porsi');
            $table->dropColumn('item_menu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreignId('menu_id')->nullable()->constrained('menu')->onDelete('set null');
            $table->integer('porsi')->nullable();
            $table->json('item_menu')->nullable();
        });

        Schema::dropIfExists('detail_pesanan_menu_item');
        Schema::dropIfExists('detail_pesanan');
    }
};
