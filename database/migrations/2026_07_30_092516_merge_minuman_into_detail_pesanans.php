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
        // 1. Modifikasi detail_pesanan
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_id')->nullable()->change();
            $table->foreignId('minuman_id')->nullable()->constrained('minumans')->nullOnDelete();
        });

        // 2. Hapus detail_pesanan_minumans
        Schema::dropIfExists('detail_pesanan_minumans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('detail_pesanan_minumans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->foreignId('minuman_id')->nullable()->constrained('minumans')->nullOnDelete();
            $table->integer('jumlah');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['minuman_id']);
            $table->dropColumn('minuman_id');
            // Reverting menu_id to not nullable could cause data loss if there are rows with null menu_id.
            // But for a full rollback we ideally would delete those rows first. 
            // We will just leave it nullable in the down method to prevent rollback errors.
        });
    }
};
