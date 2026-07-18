<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Order merepresentasikan keseluruhan transaksi pesanan dari pelanggan.
 * Menampung informasi tujuan pengiriman, ringkasan harga (subtotal & ongkir),
 * status transaksi (dari Midtrans), dan metode pembayaran.
 */
class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'catering_service_id', 'package_id',
        'order_date', 'event_start_time', 'pickup_method', 'district_id', 'village_id',
        'address_detail', 'latitude', 'longitude', 'serving_type', 'portion', 'subtotal',
        'shipping_cost', 'total', 'payment_method', 'payment_status', 'refund_status',
        'midtrans_snap_token', 'midtrans_transaction_id', 'status',
        'cancellation_reason', 'cancelled_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'cancelled_at' => 'datetime',
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
            'pending_payment' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'on_delivery' => 'Sedang Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'yellow',
            'processing' => 'blue',
            'on_delivery' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
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
        return $query->where('status', 'completed');
    }

    // === Order Number Generation ===

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

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(CateringPackage::class, 'package_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
