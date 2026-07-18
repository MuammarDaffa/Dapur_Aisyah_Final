<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model CustomOption menyimpan data kustomisasi atau opsi fleksibel dalam pemesanan.
 * Ini bisa berupa tipe penyajian, dekorasi, atau ekstra lauk yang dapat dipilih oleh pelanggan.
 */
class CustomOption extends Model
{
    protected $fillable = [
        'catering_service_id', 'type', 'name', 'price', 'min_qty', 'is_active', 'image', 'items',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'min_qty' => 'integer',
            'is_active' => 'boolean',
            'items' => 'array',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // === Relationships ===

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    /**
     * Paket-paket yang menyertakan custom option ini.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(CateringPackage::class, 'catering_package_custom_option')
                    ->withPivot('quantity');
    }

    /**
     * Harga terformat
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
