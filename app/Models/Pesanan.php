<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    public const STATUS_BELUM_BAYAR = 'belum_bayar';
    public const STATUS_DP = 'dp';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_DIBATALKAN = 'dibatalkan';

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
        'refund_status',

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
            self::STATUS_BELUM_BAYAR => 'Belum Bayar',
            self::STATUS_DP => 'DP',
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_BELUM_BAYAR => 'yellow',
            self::STATUS_DP => 'blue',
            self::STATUS_LUNAS => 'green',
            self::STATUS_DIBATALKAN => 'red',
            default => 'gray',
        };
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_LUNAS);
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

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
