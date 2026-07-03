<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CateringService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'serving_types', 'min_portion',
        'max_portion', 'base_price', 'order_terms', 'schedule_notes',
        'minimal_order_days',
        'service_area', 'available_features', 'is_active', 'image',
    ];

    protected function casts(): array
    {
        return [
            'serving_types' => 'array',
            'service_area' => 'array',
            'available_features' => 'array',
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    // === Feature Helpers ===

    /**
     * Cek apakah layanan memiliki fitur tertentu.
     * Fitur: 'daily_menu', 'packages', 'full_custom'
     */
    public function hasFeature(string $feature): bool
    {
        return is_array($this->available_features)
            && in_array($feature, $this->available_features);
    }

    /**
     * Apakah layanan tipe Harian (berbasis produk)?
     */
    public function isDaily(): bool
    {
        return $this->hasFeature('daily_menu');
    }

    /**
     * Apakah layanan tipe Event (paket dan/atau full custom)?
     */
    public function isEvent(): bool
    {
        return $this->hasFeature('packages') || $this->hasFeature('full_custom');
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDaily($query)
    {
        return $query->whereJsonContains('available_features', 'daily_menu');
    }

    public function scopeEvent($query)
    {
        return $query->where(function ($q) {
            $q->whereJsonContains('available_features', 'packages')
              ->orWhereJsonContains('available_features', 'full_custom');
        });
    }

    // === Accessors ===

    /**
     * Label tipe katering: Daily / Event / -
     */
    public function getTypeLabelAttribute(): string
    {
        if ($this->isDaily()) return 'Daily';
        if ($this->isEvent()) return 'Event';
        return '-';
    }

    // === Relationships ===

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function menuPeriods(): HasMany
    {
        return $this->hasMany(MenuPeriod::class);
    }


    public function packages(): HasMany
    {
        return $this->hasMany(CateringPackage::class);
    }

    public function customOptions(): HasMany
    {
        return $this->hasMany(CustomOption::class);
    }

    /**
     * Shortcut: Extra yang tersedia untuk layanan ini.
     */
    public function extras(): HasMany
    {
        return $this->hasMany(CustomOption::class)->where('type', 'extra');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
