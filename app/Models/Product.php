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
        'image', 'is_best_seller', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
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
        return $query->where('is_active', true);
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    // === Accessors ===

    /**
     * Cek apakah produk aktif.
     */
    public function isAvailable(): bool
    {
        return $this->is_active;
    }

    /**
     * Cek apakah produk habis (status habis sekarang diatur per tanggal pada MenuPeriodItem).
     */
    public function isOutOfStock(): bool
    {
        return false;
    }

    // === Relationships ===

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }


    public function extras(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CustomOption::class, 'custom_option_product');
    }

    public function menuPeriodItems(): HasMany
    {
        return $this->hasMany(\App\Models\MenuPeriodItem::class);
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
