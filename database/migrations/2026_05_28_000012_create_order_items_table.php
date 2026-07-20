<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->foreignId('opsi_kustom_id')->nullable()->constrained('opsi_kustom')->nullOnDelete();
            $table->string('item_name', 150); // Snapshot nama saat pesanan
            $table->integer('jumlah')->default(1);
            $table->decimal('unit_price', 12, 2); // Snapshot harga saat pesanan
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
