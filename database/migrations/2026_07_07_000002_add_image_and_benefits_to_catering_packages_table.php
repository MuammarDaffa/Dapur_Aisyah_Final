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
        Schema::table('paket_katering', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
            $table->json('benefits')->nullable()->after('total_portions');
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('serving_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_katering', function (Blueprint $table) {
            $table->dropColumn(['image', 'benefits']);
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
