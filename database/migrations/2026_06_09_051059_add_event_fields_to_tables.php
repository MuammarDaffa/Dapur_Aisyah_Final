<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. catering_packages: tambah kolom event
        Schema::table('catering_packages', function (Blueprint $table) {
            $table->integer('total_portions')->default(0)->after('price');
            $table->integer('min_addition_qty')->default(0)->after('total_portions');
        });

        // 2. custom_options: ubah type dari ENUM ke VARCHAR, tambah min_qty
        // Ubah ENUM ke VARCHAR agar bisa menambah tipe baru tanpa migrasi
        DB::statement("ALTER TABLE custom_options MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'menu'");

        Schema::table('custom_options', function (Blueprint $table) {
            $table->integer('min_qty')->default(0)->after('price');
        });

        // 3. carts: tambah kolom grouping event
        Schema::table('carts', function (Blueprint $table) {
            $table->string('cart_group_id', 36)->nullable()->after('user_id');
            $table->foreignId('catering_package_id')->nullable()->after('custom_option_id')
                  ->constrained('catering_packages')->nullOnDelete();
            $table->string('item_type', 20)->default('product')->after('quantity');
            $table->index('cart_group_id');
        });

        // 4. Pivot table: isi paket (menu/dekorasi/penyajian/extra dalam paket)
        Schema::create('catering_package_custom_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_package_id')->constrained('catering_packages')->cascadeOnDelete();
            $table->foreignId('custom_option_id')->constrained('custom_options')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->unique(['catering_package_id', 'custom_option_id'], 'pkg_opt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_package_custom_option');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['catering_package_id']);
            $table->dropIndex(['cart_group_id']);
            $table->dropColumn(['cart_group_id', 'catering_package_id', 'item_type']);
        });

        Schema::table('custom_options', function (Blueprint $table) {
            $table->dropColumn('min_qty');
        });

        DB::statement("ALTER TABLE custom_options MODIFY COLUMN type ENUM('menu','decoration','serving_type','extra') NOT NULL DEFAULT 'menu'");

        Schema::table('catering_packages', function (Blueprint $table) {
            $table->dropColumn(['total_portions', 'min_addition_qty']);
        });
    }
};
