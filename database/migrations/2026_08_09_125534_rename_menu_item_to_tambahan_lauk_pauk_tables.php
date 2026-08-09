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
        Schema::rename('menu_item', 'tambahan_lauk_pauk');
        
        // Due to foreign key constraints, renaming the column on a pivot table directly 
        // might require dropping the constraint first depending on DB engine.
        // It's safer to drop foreign key, rename column, and recreate foreign key.
        Schema::table('detail_pesanan_menu_item', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
        });

        Schema::rename('detail_pesanan_menu_item', 'detail_tambahan_lauk_pauk');

        Schema::table('detail_tambahan_lauk_pauk', function (Blueprint $table) {
            $table->renameColumn('menu_item_id', 'tambahan_lauk_pauk_id');
            $table->foreign('tambahan_lauk_pauk_id')->references('id')->on('tambahan_lauk_pauk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_tambahan_lauk_pauk', function (Blueprint $table) {
            $table->dropForeign(['tambahan_lauk_pauk_id']);
            $table->renameColumn('tambahan_lauk_pauk_id', 'menu_item_id');
            $table->foreign('menu_item_id')->references('id')->on('menu_item')->onDelete('cascade');
        });

        Schema::rename('detail_tambahan_lauk_pauk', 'detail_pesanan_menu_item');
        Schema::rename('tambahan_lauk_pauk', 'menu_item');
    }
};
