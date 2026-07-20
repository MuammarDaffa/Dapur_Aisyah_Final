<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('produk_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->foreignId('opsi_kustom_id')->nullable()->constrained('opsi_kustom')->nullOnDelete();
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
