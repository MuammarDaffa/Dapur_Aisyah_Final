<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ongkos_kirim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->decimal('cost', 10, 2)->default(20000);
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ongkos_kirim');
    }
};
