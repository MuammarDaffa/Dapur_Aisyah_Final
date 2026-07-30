<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    public const PEMBAYARAN_BELUM_DIBAYAR = 'belum_dibayar';
    public const PEMBAYARAN_DP = 'dp';
    public const PEMBAYARAN_LUNAS = 'lunas';

    public const PESANAN_DIPROSES = 'diproses';
    public const PESANAN_DIBATALKAN = 'dibatalkan';
    public const PESANAN_SELESAI = 'selesai';

    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'layanan_id',
        'menu_id',
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
        'jumlah_dp',
        'sisa_pembayaran',
        'refund_status',
        'status_pembayaran',
        'status_pesanan',
        'alasan_pembatalan',
        'dibatalkan_pada',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesanan' => 'date',
            'subtotal' => 'integer',
            'total' => 'integer',
            'dibatalkan_pada' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            self::PEMBAYARAN_BELUM_DIBAYAR => 'Belum Dibayar',
            self::PEMBAYARAN_DP => 'DP ',
            self::PEMBAYARAN_LUNAS => 'Lunas',
            default => $this->status_pembayaran,
        };
    }

    public function getStatusPembayaranColorAttribute(): string
    {
        return match ($this->status_pembayaran) {
            self::PEMBAYARAN_BELUM_DIBAYAR => 'danger',
            self::PEMBAYARAN_DP => 'warning text-dark',
            self::PEMBAYARAN_LUNAS => 'success',
            default => 'secondary',
        };
    }

    public function getStatusPesananLabelAttribute(): ?string
    {
        if (is_null($this->status_pesanan)) {
            return null;
        }

        return match ($this->status_pesanan) {
            self::PESANAN_DIPROSES => 'Diproses',
            self::PESANAN_DIBATALKAN => 'Dibatalkan',
            self::PESANAN_SELESAI => 'Selesai',
            default => $this->status_pesanan,
        };
    }

    public function getStatusPesananColorAttribute(): ?string
    {
        if (is_null($this->status_pesanan)) {
            return null;
        }

        return match ($this->status_pesanan) {
            self::PESANAN_DIPROSES => 'info',
            self::PESANAN_DIBATALKAN => 'danger',
            self::PESANAN_SELESAI => 'success',
            default => 'secondary',
        };
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_pesanan', self::PESANAN_SELESAI);
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

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

}
