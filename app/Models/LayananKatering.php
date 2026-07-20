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
        'name', 'slug', 'serving_types',
        'minimal_order_days',
        'service_area', 'fitur_tersedia', 'is_active', 'image',
    ];

    protected function casts(): array
    {
        return [
            'serving_types' => 'array',
            'service_area' => 'array',
            'fitur_tersedia' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($service) {
            if (empty($service->slug) && !empty($service->name)) {
                $slug = Str::slug($service->name);
                $originalSlug = $slug;
                $count = 1;
                
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $service->slug = $slug;
            }
        });
    }

    // === Feature Helpers ===

    /**
     * Cek apakah layanan memiliki fitur tertentu.
     * Fitur: 'menu_harian', 'paket', 'kustom_penuh'
     */
    /**
         * Memeriksa apakah layanan ini mendukung fitur tertentu (misal: menu_harian, paket, kustom_penuh).
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
    public function isHarian(): bool
    {
        return $this->hasFeature('menu_harian');
    }

    /**
     * Apakah layanan tipe Event (paket dan/atau full custom)?
     */
    public function isAcara(): bool
    {
        return $this->hasFeature('paket') || $this->hasFeature('kustom_penuh');
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHarian($query)
    {
        return $query->whereJsonContains('fitur_tersedia', 'menu_harian');
    }

    public function scopeAcara($query)
    {
        return $query->where(function ($q) {
            $q->whereJsonContains('fitur_tersedia', 'paket')
              ->orWhereJsonContains('fitur_tersedia', 'kustom_penuh');
        });
    }

    // === Accessors ===

    /**
     * Label tipe katering: Harian / Acara / -
     */
    public function getTypeLabelAttribute(): string
    {
        if ($this->isHarian()) return 'Harian';
        if ($this->isAcara()) return 'Acara';
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
