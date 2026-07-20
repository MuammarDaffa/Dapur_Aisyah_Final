<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model PaketKatering merepresentasikan paket bundling yang ditawarkan (misal: Paket Hemat, Paket Premium).
 * Paket ini memiliki relasi dengan OpsiKustom untuk menentukan isi paket seperti menu, dekorasi, dll.
 */
class PaketKatering extends Model
{
    protected $table = 'paket_katering';

    protected $fillable = [
        'layanan_katering_id', 'name', 'image', 'deskripsi', 'harga',
        'total_portions', 'benefits',
        'is_custom', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
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

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }

    /**
     * Custom options yang termasuk dalam paket ini.
     * Pivot menyimpan jumlah (porsi default per item).
     */
    public function opsiKustom(): BelongsToMany
    {
        return $this->belongsToMany(OpsiKustom::class, 'catering_package_custom_option')
                    ->withPivot('jumlah');
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
        return $this->opsiKustom()->where('type', 'menu')->get();
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
        return $this->opsiKustom()->where('type', 'decoration')->get();
    }

    /**
     * Mendapatkan penyajian yang termasuk dalam paket.
     */
    /**
         * Mengambil daftar opsi bertipe 'tipe_penyajian' (tipe penyajian) yang tersedia untuk paket ini.
         * Jika tidak ada batasan khusus di paket, akan mengembalikan semua tipe penyajian yang aktif.
         * @return \Illuminate\Database\Eloquent\Collection
         */
    public function getIncludedServingTypes()
    {
        $included = $this->opsiKustom()->where('type', 'tipe_penyajian')->get();
        if ($included->isEmpty()) {
            return \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->where('is_active', true)->get();
        }
        return $included;
    }

    /**
     * Mendapatkan extra yang termasuk dalam paket.
     */
    public function getIncludedExtras()
    {
        return $this->opsiKustom()->where('type', 'extra')->get();
    }

    /**
     * Harga terformat
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
