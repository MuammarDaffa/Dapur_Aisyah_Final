<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catering_services', function (Blueprint $table) {
            $table->integer('max_portion')->nullable()->after('min_portion');
        });
    }

    public function down(): void
    {
        Schema::table('catering_services', function (Blueprint $table) {
            $table->dropColumn('max_portion');
        });
    }
};
