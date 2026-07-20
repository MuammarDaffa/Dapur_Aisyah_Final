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
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi',
                'base_price',
                'min_portion',
                'maksimal_porsi',
                'order_terms',
                'schedule_notes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanan_katering', function (Blueprint $table) {
            $table->text('deskripsi')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->integer('min_portion')->default(1);
            $table->integer('maksimal_porsi')->nullable();
            $table->text('order_terms')->nullable();
            $table->text('schedule_notes')->nullable();
        });
    }
};
