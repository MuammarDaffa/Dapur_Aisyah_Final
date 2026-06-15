<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'catering_service_id', 'name', 'slug', 'description', 'price',
        'image', 'is_best_seller', 'available_days', 'is_active', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'available_days' => 'array',
            'is_best_seller' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    // === Accessors ===

    /**
     * Cek apakah produk tersedia untuk dipesan.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia' && $this->is_active;
    }

    /**
     * Cek apakah produk habis.
     */
    public function isOutOfStock(): bool
    {
        return $this->status === 'habis';
    }

    // === Relationships ===

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Mendapatkan harga yang terformat
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
