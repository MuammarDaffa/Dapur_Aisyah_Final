<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model CateringPackage merepresentasikan paket bundling yang ditawarkan (misal: Paket Hemat, Paket Premium).
 * Paket ini memiliki relasi dengan CustomOption untuk menentukan isi paket seperti menu, dekorasi, dll.
 */
class CateringPackage extends Model
{
    protected $fillable = [
        'catering_service_id', 'name', 'image', 'description', 'price',
        'total_portions', 'benefits',
        'is_custom', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total_portions' => 'integer',
            'benefits' => 'array',
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
    /**
         * Mengambil daftar opsi bertipe 'menu' (makanan/minuman) yang termasuk dalam paket ini.
         * @return \Illuminate\Database\Eloquent\Collection
         */
    public function getIncludedMenus()
    {
        return $this->customOptions()->where('type', 'menu')->get();
    }

    /**
     * Mendapatkan dekorasi yang termasuk dalam paket.
     */
    /**
         * Mengambil daftar opsi bertipe 'decoration' (dekorasi) yang disertakan dalam paket ini.
         * @return \Illuminate\Database\Eloquent\Collection
         */
    public function getIncludedDecorations()
    {
        return $this->customOptions()->where('type', 'decoration')->get();
    }

    /**
     * Mendapatkan penyajian yang termasuk dalam paket.
     */
    /**
         * Mengambil daftar opsi bertipe 'serving_type' (tipe penyajian) yang tersedia untuk paket ini.
         * Jika tidak ada batasan khusus di paket, akan mengembalikan semua tipe penyajian yang aktif.
         * @return \Illuminate\Database\Eloquent\Collection
         */
    public function getIncludedServingTypes()
    {
        $included = $this->customOptions()->where('type', 'serving_type')->get();
        if ($included->isEmpty()) {
            return \App\Models\CustomOption::where('type', 'serving_type')->where('is_active', true)->get();
        }
        return $included;
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
