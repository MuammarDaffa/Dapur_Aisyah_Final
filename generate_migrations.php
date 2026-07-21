<?php
$timestamp = date('Y_m_d_His');
$migration1 = "database/migrations/{$timestamp}_create_menu_harians_table.php";
$migration2 = "database/migrations/{$timestamp}_create_menu_harian_extras_table.php";
$migration3 = "database/migrations/{$timestamp}_update_keranjang_and_pesanan_for_menu_harian.php";
$migration4 = "database/migrations/{$timestamp}_drop_old_catering_tables.php";

$content1 = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_harian', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('layanan_katering_id')->constrained('layanan_katering')->cascadeOnDelete();
            \$table->string('hari');
            \$table->date('tanggal')->nullable();
            \$table->string('nama_menu')->nullable();
            \$table->bigInteger('harga')->nullable();
            \$table->integer('stok_awal')->nullable();
            \$table->integer('stok_tersisa')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_harian');
    }
};
EOT;

$content2 = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_harian_extra', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('menu_harian_id')->constrained('menu_harian')->cascadeOnDelete();
            \$table->string('nama_extra');
            \$table->bigInteger('harga');
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_harian_extra');
    }
};
EOT;

$content3 = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint \$table) {
            \$table->dropForeign(['produk_id']);
            \$table->dropColumn('produk_id');
            \$table->foreignId('menu_harian_id')->nullable()->after('cart_group_id')->constrained('menu_harian')->nullOnDelete();
        });
        
        Schema::table('detail_pesanan', function (Blueprint \$table) {
            \$table->dropForeign(['produk_id']);
            \$table->dropColumn('produk_id');
            \$table->foreignId('menu_harian_id')->nullable()->after('pesanan_id')->constrained('menu_harian')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint \$table) {
            \$table->dropForeign(['menu_harian_id']);
            \$table->dropColumn('menu_harian_id');
            // Can't reliably recreate produk_id without the table
        });
        
        Schema::table('detail_pesanan', function (Blueprint \$table) {
            \$table->dropForeign(['menu_harian_id']);
            \$table->dropColumn('menu_harian_id');
        });
    }
};
EOT;

$content4 = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('item_periode_menu');
        Schema::dropIfExists('periode_menu');
        Schema::dropIfExists('produk');
    }

    public function down(): void
    {
        // One way migration
    }
};
EOT;

file_put_contents(__DIR__ . '/' . $migration1, $content1);
sleep(1);
$timestamp = date('Y_m_d_His');
$migration2 = "database/migrations/{$timestamp}_create_menu_harian_extras_table.php";
file_put_contents(__DIR__ . '/' . $migration2, $content2);
sleep(1);
$timestamp = date('Y_m_d_His');
$migration3 = "database/migrations/{$timestamp}_update_keranjang_and_pesanan_for_menu_harian.php";
file_put_contents(__DIR__ . '/' . $migration3, $content3);
sleep(1);
$timestamp = date('Y_m_d_His');
$migration4 = "database/migrations/{$timestamp}_drop_old_catering_tables.php";
file_put_contents(__DIR__ . '/' . $migration4, $content4);

echo "Migrations created.\n";
