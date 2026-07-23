<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Pesanan merepresentasikan keseluruhan transaksi pesanan dari pelanggan.
 * Menampung informasi tujuan pengiriman, ringkasan harga (subtotal),
 * status transaksi (dari Midtrans), dan metode pembayaran.
 */
class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'nomor_pesanan', 'user_id', 'layanan_katering_id', 'paket_katering_id',
        'tanggal_pesanan', 'event_start_time', 'metode_pengambilan',
        'detail_alamat', 'latitude', 'longitude', 'tipe_penyajian', 'porsi', 'subtotal',
        'total', 'metode_pembayaran', 'status_pembayaran', 'refund_status',
        'midtrans_snap_token', 'midtrans_transaction_id', 'status',
        'alasan_pembatalan', 'dibatalkan_pada', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesanan' => 'date',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'dibatalkan_pada' => 'datetime',
        ];
    }

    // === Status Labels ===

    /**
         * Mengonversi status pesanan database ke label Bahasa Indonesia yang mudah dipahami (UI-Friendly).
         * @return string
         */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'diproses' => 'Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_pembayaran' => 'yellow',
            'diproses' => 'blue',
            'dikirim' => 'purple',
            'selesai' => 'green',
            'dibatalkan' => 'red',
            default => 'gray',
        };
    }

    // === Scopes ===

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    // === Pesanan Number Generation ===

    /**
         * Membuat nomor pesanan unik dengan memanggil Service khusus pemesanan.
         * @return string
         */
    public static function generateOrderNumber(): string
    {
        return \App\Services\OrderService::generateOrderNumber();
    }


    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }




    public function items(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }


    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class);
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class);
    }
}
