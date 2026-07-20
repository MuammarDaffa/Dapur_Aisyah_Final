<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('available_days');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('available_days')->nullable()->after('is_best_seller');
        });
    }
};
