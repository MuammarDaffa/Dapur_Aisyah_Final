<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('periode_menu', 'nama_periode')) {
            Schema::table('periode_menu', function (Blueprint $table) {
                $table->dropColumn('nama_periode');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('periode_menu', 'nama_periode')) {
            Schema::table('periode_menu', function (Blueprint $table) {
                $table->string('nama_periode', 150)->nullable();
            });
        }
    }
};
