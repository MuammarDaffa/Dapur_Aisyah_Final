<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'catering_service_id', 'cart_group_id', 'product_id',
        'custom_option_id', 'catering_package_id', 'extras',
        'quantity', 'item_type', 'serving_type_id',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customOption(): BelongsTo
    {
        return $this->belongsTo(CustomOption::class);
    }

    public function cateringPackage(): BelongsTo
    {
        return $this->belongsTo(CateringPackage::class);
    }

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    public function servingType(): BelongsTo
    {
        return $this->belongsTo(CustomOption::class, 'serving_type_id');
    }

    // === Helpers ===

    /**
     * Cek apakah item ini adalah bagian dari event order.
     */
    public function isEventItem(): bool
    {
        return !empty($this->cart_group_id);
    }

    /**
     * Cek apakah item ini adalah harian (bukan event).
     */
    public function isDailyItem(): bool
    {
        return empty($this->cart_group_id);
    }

    /**
     * Cek apakah item ini sudah termasuk dalam paket (harga = 0).
     */
    public function isBundledInPackage(): bool
    {
        return $this->item_type === 'package_item';
    }

    /**
     * Menghitung subtotal item keranjang.
     * Item yang termasuk dalam paket (package_item) = 0.
     * Item tambahan dihitung normal.
     * Item tipe 'package' = harga paket itu sendiri.
     */
    public function getSubtotalAttribute(): float
    {
        // Item paket (isi paket) → harga 0, sudah termasuk harga paket
        if ($this->item_type === 'package_item') {
            return 0;
        }

        // Item tipe package → harga paket
        if ($this->item_type === 'package' && $this->cateringPackage) {
            return (float) $this->cateringPackage->price;
        }

        // Item produk harian / tambahan event
        $basePrice = $this->product
            ? (float) $this->product->price
            : ($this->customOption ? (float) $this->customOption->price : 0);

        $extrasPrice = 0;
        if (!empty($this->extras)) {
            $extraIds = array_column($this->extras, 'id');
            $extras = \App\Models\CustomOption::whereIn('id', $extraIds)->get()->keyBy('id');
            
            foreach ($this->extras as $extraData) {
                if ($extra = $extras->get($extraData['id'])) {
                    $extrasPrice += ((float) $extra->price * $extraData['qty']);
                }
            }
        }

        return ($basePrice * $this->quantity) + $extrasPrice;
    }
}
