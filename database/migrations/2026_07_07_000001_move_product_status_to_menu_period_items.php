<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom status ke item_periode_menu
        if (!Schema::hasColumn('item_periode_menu', 'status')) {
            Schema::table('item_periode_menu', function (Blueprint $table) {
                $table->string('status', 20)->default('tersedia')->after('menu_date');
            });
        }

        // 2. Migrasi data: salin status dari tabel produk ke item_periode_menu
        if (Schema::hasColumn('produk', 'status')) {
            $produk = DB::table('produk')->get(['id', 'status']);
            foreach ($produk as $produk) {
                if ($produk->status) {
                    DB::table('item_periode_menu')
                        ->where('produk_id', $produk->id)
                        ->update(['status' => $produk->status]);
                }
            }

            // 3. Hapus kolom status dari tabel produk untuk menghindari duplikasi sumber data
            Schema::table('produk', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom status ke tabel produk
        if (!Schema::hasColumn('produk', 'status')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->string('status', 20)->default('tersedia')->after('is_active');
            });
        }

        // Hapus kolom status dari item_periode_menu
        if (Schema::hasColumn('item_periode_menu', 'status')) {
            Schema::table('item_periode_menu', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
