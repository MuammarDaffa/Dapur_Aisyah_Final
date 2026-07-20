<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('layanan_katering_id')->constrained('layanan_katering');
            $table->foreignId('paket_katering_id')->nullable()->constrained('paket_katering')->nullOnDelete();
            $table->date('tanggal_pesanan'); // Tanggal acara/pengiriman
            $table->enum('metode_pengambilan', ['pickup', 'delivery']);
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')->nullOnDelete();
            $table->foreignId('desa_id')->nullable()->constrained('desa')->nullOnDelete();
            $table->text('detail_alamat')->nullable();
            $table->string('tipe_penyajian', 50)->nullable();
            $table->integer('porsi')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('ongkos_kirim', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('metode_pembayaran', ['transfer']);
            $table->enum('status_pembayaran', ['belum_dibayar', 'sudah_dibayar', 'gagal'])->default('belum_dibayar');
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->string('midtrans_transaction_id', 255)->nullable();
            $table->enum('status', ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu_pembayaran');
            $table->text('alasan_pembatalan')->nullable();
            $table->timestamp('dibatalkan_pada')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes untuk performa query
            $table->index('nomor_pesanan');
            $table->index('status');
            $table->index('status_pembayaran');
            $table->index('tanggal_pesanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
