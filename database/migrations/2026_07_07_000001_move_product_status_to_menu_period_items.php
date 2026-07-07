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
        // 1. Tambahkan kolom status ke menu_period_items
        if (!Schema::hasColumn('menu_period_items', 'status')) {
            Schema::table('menu_period_items', function (Blueprint $table) {
                $table->string('status', 20)->default('tersedia')->after('menu_date');
            });
        }

        // 2. Migrasi data: salin status dari tabel products ke menu_period_items
        if (Schema::hasColumn('products', 'status')) {
            $products = DB::table('products')->get(['id', 'status']);
            foreach ($products as $product) {
                if ($product->status) {
                    DB::table('menu_period_items')
                        ->where('product_id', $product->id)
                        ->update(['status' => $product->status]);
                }
            }

            // 3. Hapus kolom status dari tabel products untuk menghindari duplikasi sumber data
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom status ke tabel products
        if (!Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('status', 20)->default('tersedia')->after('is_active');
            });
        }

        // Hapus kolom status dari menu_period_items
        if (Schema::hasColumn('menu_period_items', 'status')) {
            Schema::table('menu_period_items', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
