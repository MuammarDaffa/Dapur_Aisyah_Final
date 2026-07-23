<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'layanan_id',
        'tanggal_pesanan',
        'event_start_time',
        'metode_pengambilan',
        'detail_alamat',
        'latitude',
        'longitude',
        'tipe_penyajian',
        'porsi',
        'subtotal',
        'total',
        'metode_pembayaran',
        'status_pembayaran',
        'refund_status',
        'midtrans_snap_token',
        'midtrans_transaction_id',
        'status',
        'alasan_pembatalan',
        'dibatalkan_pada',
        'catatan',
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::where('nomor_pesanan', 'like', "ORD-{$date}-%")
                            ->orderBy('nomor_pesanan', 'desc')
                            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->nomor_pesanan, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "ORD-{$date}-{$nextNumber}";
    }

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, 'pesanan_id');
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class, 'pesanan_id');
    }
}
