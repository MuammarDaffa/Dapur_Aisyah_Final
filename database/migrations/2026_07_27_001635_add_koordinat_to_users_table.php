<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan 2 laci baru. 'nullable' berarti boleh kosong
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus laci jika kita ingin membatalkan migration
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
