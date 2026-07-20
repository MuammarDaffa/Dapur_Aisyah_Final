<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. paket_katering: tambah kolom event
        Schema::table('paket_katering', function (Blueprint $table) {
            $table->integer('total_portions')->default(0)->after('harga');
            $table->integer('min_addition_qty')->default(0)->after('total_portions');
        });

        // 2. opsi_kustom: ubah type dari ENUM ke VARCHAR, tambah min_qty
        // Ubah ENUM ke VARCHAR agar bisa menambah tipe baru tanpa migrasi
        DB::statement("ALTER TABLE opsi_kustom MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'menu'");

        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->integer('min_qty')->default(0)->after('harga');
        });

        // 3. keranjang: tambah kolom grouping event
        Schema::table('keranjang', function (Blueprint $table) {
            $table->string('cart_group_id', 36)->nullable()->after('user_id');
            $table->foreignId('catering_package_id')->nullable()->after('opsi_kustom_id')
                  ->constrained('paket_katering')->nullOnDelete();
            $table->string('item_type', 20)->default('produk')->after('jumlah');
            $table->index('cart_group_id');
        });

        // 4. Pivot table: isi paket (menu/dekorasi/penyajian/extra dalam paket)
        Schema::create('catering_package_custom_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_package_id')->constrained('paket_katering')->cascadeOnDelete();
            $table->foreignId('opsi_kustom_id')->constrained('opsi_kustom')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->unique(['catering_package_id', 'opsi_kustom_id'], 'pkg_opt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_package_custom_option');

        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign(['catering_package_id']);
            $table->dropIndex(['cart_group_id']);
            $table->dropColumn(['cart_group_id', 'catering_package_id', 'item_type']);
        });

        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->dropColumn('min_qty');
        });

        DB::statement("ALTER TABLE opsi_kustom MODIFY COLUMN type ENUM('menu','decoration','tipe_penyajian','extra') NOT NULL DEFAULT 'menu'");

        Schema::table('paket_katering', function (Blueprint $table) {
            $table->dropColumn(['total_portions', 'min_addition_qty']);
        });
    }
};
