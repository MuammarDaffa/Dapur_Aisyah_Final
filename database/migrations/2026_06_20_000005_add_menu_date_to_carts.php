<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->date('menu_date')->nullable()->after('extras');
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropColumn('menu_date');
        });
    }
};
