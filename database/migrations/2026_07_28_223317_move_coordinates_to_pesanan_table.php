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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'latitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
            }
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
