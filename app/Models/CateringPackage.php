<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CateringPackage extends Model
{
    protected $fillable = [
        'catering_service_id', 'name', 'description', 'price',
        'total_portions', 'min_addition_qty',
        'is_custom', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total_portions' => 'integer',
            'min_addition_qty' => 'integer',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // === Relationships ===

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    /**
     * Custom options yang termasuk dalam paket ini.
     * Pivot menyimpan quantity (porsi default per item).
     */
    public function customOptions(): BelongsToMany
    {
        return $this->belongsToMany(CustomOption::class, 'catering_package_custom_option')
                    ->withPivot('quantity');
    }

    // === Helpers ===

    /**
     * Mendapatkan menu yang termasuk dalam paket.
     */
    public function getIncludedMenus()
    {
        return $this->customOptions()->where('type', 'menu')->get();
    }

    /**
     * Mendapatkan dekorasi yang termasuk dalam paket.
     */
    public function getIncludedDecorations()
    {
        return $this->customOptions()->where('type', 'decoration')->get();
    }

    /**
     * Mendapatkan penyajian yang termasuk dalam paket.
     */
    public function getIncludedServingTypes()
    {
        return $this->customOptions()->where('type', 'serving_type')->get();
    }

    /**
     * Mendapatkan extra yang termasuk dalam paket.
     */
    public function getIncludedExtras()
    {
        return $this->customOptions()->where('type', 'extra')->get();
    }

    /**
     * Harga terformat
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
