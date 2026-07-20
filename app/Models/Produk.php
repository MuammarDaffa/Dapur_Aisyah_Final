<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Produk merepresentasikan menu individual (makanan/minuman) yang ditawarkan.
 * Model ini menjadi entitas utama dalam fitur pemesanan Katering Harian.
 */
class Produk extends Model
{
    protected $table = 'produk';

    use HasFactory;

    protected $fillable = [
        'layanan_katering_id', 'name', 'slug', 'deskripsi', 'harga',
        'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($produk) {
            if (empty($produk->slug)) {
                $produk->slug = Str::slug($produk->name);
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

    // === Accessors ===

    /**
     * Cek apakah produk aktif.
     */
    public function isAvailable(): bool
    {
        return $this->is_active;
    }

    /**
     * Cek apakah produk habis (status habis sekarang diatur per tanggal pada ItemPeriodeMenu).
     */
    public function isOutOfStock(): bool
    {
        return false;
    }

    // === Relationships ===

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }


    public function extras(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(OpsiKustom::class, 'custom_option_product');
    }

    public function itemPeriodeMenu(): HasMany
    {
        return $this->hasMany(\App\Models\ItemPeriodeMenu::class);
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    // Mendapatkan harga yang terformat
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
