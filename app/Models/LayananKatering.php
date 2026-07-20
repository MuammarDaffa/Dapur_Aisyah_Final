<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model LayananKatering merepresentasikan jenis layanan utama (Katering Harian, Prasmanan, dll).
 * Model ini menjadi pusat konfigurasi untuk harga dasar, batas porsi, dan fitur layanan.
 */
class LayananKatering extends Model
{
    protected $table = 'layanan_katering';

    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'deskripsi', 'serving_types', 'min_portion',
        'maksimal_porsi', 'base_price', 'order_terms', 'schedule_notes',
        'minimal_order_days',
        'service_area', 'fitur_tersedia', 'is_active', 'image',
    ];

    protected function casts(): array
    {
        return [
            'serving_types' => 'array',
            'service_area' => 'array',
            'fitur_tersedia' => 'array',
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
    /**
         * Memeriksa apakah layanan ini mendukung fitur tertentu (misal: daily_menu, packages, full_custom).
         * @param string $feature
         * @return bool
         */
    public function hasFeature(string $feature): bool
    {
        return is_array($this->fitur_tersedia)
            && in_array($feature, $this->fitur_tersedia);
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
        return $query->whereJsonContains('fitur_tersedia', 'daily_menu');
    }

    public function scopeEvent($query)
    {
        return $query->where(function ($q) {
            $q->whereJsonContains('fitur_tersedia', 'packages')
              ->orWhereJsonContains('fitur_tersedia', 'full_custom');
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

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    public function periodeMenu(): HasMany
    {
        return $this->hasMany(PeriodeMenu::class);
    }


    public function packages(): HasMany
    {
        return $this->hasMany(PaketKatering::class);
    }

    public function opsiKustom(): HasMany
    {
        return $this->hasMany(OpsiKustom::class);
    }

    /**
     * Shortcut: Extra yang tersedia untuk layanan ini.
     */
    public function extras(): HasMany
    {
        return $this->hasMany(OpsiKustom::class)->where('type', 'extra');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}
