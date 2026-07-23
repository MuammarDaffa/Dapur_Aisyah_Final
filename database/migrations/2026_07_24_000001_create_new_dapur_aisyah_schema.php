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
        Schema::disableForeignKeyConstraints();

        // Drop old tables if they exist in safe order
        Schema::dropIfExists('ulasan');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('extra_acara');
        Schema::dropIfExists('menu_acara');
        Schema::dropIfExists('extra_harian');
        Schema::dropIfExists('jadwal_menu');
        Schema::dropIfExists('menu_harian_extra');
        Schema::dropIfExists('menu_harian');
        Schema::dropIfExists('catering_package_custom_option');
        Schema::dropIfExists('opsi_kustom');
        Schema::dropIfExists('paket_katerings');
        Schema::dropIfExists('layanan_katering');
        Schema::dropIfExists('layanan');

        Schema::enableForeignKeyConstraints();

        // 1. layanan
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['harian', 'acara']);
            $table->integer('kapasitas_total')->nullable(); // khusus acara
            $table->integer('kapasitas_tersisa')->nullable(); // khusus acara
            $table->integer('minimal_porsi')->nullable(); // khusus acara
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. menu_harian
        Schema::create('menu_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->string('nama_menu');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. jadwal_menu
        Schema::create('jadwal_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_harian_id')->constrained('menu_harian')->cascadeOnDelete();
            $table->string('hari'); // Senin - Jumat
            $table->boolean('aktif')->default(true);
            $table->date('tanggal');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_tersisa')->default(0);
            $table->timestamps();
        });

        // 4. extra_harian
        Schema::create('extra_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_menu_id')->constrained('jadwal_menu')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('harga', 12, 2);
            $table->timestamps();
        });

        // 5. menu_acara
        Schema::create('menu_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->string('nama_menu');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_per_porsi', 12, 2);
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 6. extra_acara
        Schema::create('extra_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_acara_id')->constrained('menu_acara')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('harga', 12, 2);
            $table->string('gambar')->nullable();
            $table->timestamps();
        });

        // 7. pesanan
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->date('tanggal_pesanan');
            $table->string('event_start_time')->nullable();
            $table->string('metode_pengambilan')->default('delivery');
            $table->text('detail_alamat')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('tipe_penyajian')->nullable();
            $table->integer('porsi')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_pembayaran')->default('pending');
            $table->string('refund_status')->nullable();
            $table->string('midtrans_snap_token')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('status')->default('menunggu_pembayaran');
            $table->text('alasan_pembatalan')->nullable();
            $table->timestamp('dibatalkan_pada')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 8. detail_pesanan
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('menu_harian_id')->nullable()->constrained('menu_harian')->nullOnDelete();
            $table->foreignId('jadwal_menu_id')->nullable()->constrained('jadwal_menu')->nullOnDelete();
            $table->foreignId('extra_harian_id')->nullable()->constrained('extra_harian')->nullOnDelete();
            $table->foreignId('menu_acara_id')->nullable()->constrained('menu_acara')->nullOnDelete();
            $table->foreignId('extra_acara_id')->nullable()->constrained('extra_acara')->nullOnDelete();
            $table->string('item_name');
            $table->integer('jumlah');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
        });

        // 9. tagihan
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('nomor_tagihan')->unique();
            $table->string('service_type')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        // 10. ulasan
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ulasan');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('extra_acara');
        Schema::dropIfExists('menu_acara');
        Schema::dropIfExists('extra_harian');
        Schema::dropIfExists('jadwal_menu');
        Schema::dropIfExists('menu_harian');
        Schema::dropIfExists('layanan');
        Schema::enableForeignKeyConstraints();
    }
};
