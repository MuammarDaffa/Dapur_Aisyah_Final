<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            if (Schema::hasColumn('ulasan', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan', 'rating')) {
                $table->tinyInteger('rating')->default(5);
            }
        });
    }
};
