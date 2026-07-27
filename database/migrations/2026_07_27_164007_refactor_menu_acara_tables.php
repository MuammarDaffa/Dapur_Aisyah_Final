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
        // 1. Rename extra_acara to minuman_acara and remove its foreign key
        Schema::table('extra_acara', function (Blueprint $table) {
            $table->dropForeign(['menu_acara_id']);
            $table->dropColumn('menu_acara_id');
        });
        Schema::rename('extra_acara', 'minuman_acara');

        // 2. Drop columns from menu_acara
        Schema::table('menu_acara', function (Blueprint $table) {
            $table->dropColumn(['harga_per_porsi', 'gambar']);
        });

        // 3. Create isi_menu table
        Schema::create('isi_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_acara_id')->constrained('menu_acara')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('harga', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isi_menu');

        Schema::table('menu_acara', function (Blueprint $table) {
            $table->decimal('harga_per_porsi', 12, 2)->after('deskripsi')->default(0);
            $table->string('gambar')->nullable()->after('harga_per_porsi');
        });

        Schema::rename('minuman_acara', 'extra_acara');
        Schema::table('extra_acara', function (Blueprint $table) {
            $table->foreignId('menu_acara_id')->nullable()->after('id')->constrained('menu_acara')->cascadeOnDelete();
        });
    }
};
