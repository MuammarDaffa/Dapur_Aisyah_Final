<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model OpsiKustom menyimpan data kustomisasi atau opsi fleksibel dalam pemesanan.
 * Ini bisa berupa tipe penyajian, dekorasi, atau ekstra lauk yang dapat dipilih oleh pelanggan.
 */
class OpsiKustom extends Model
{
    protected $table = 'opsi_kustom';

    protected $fillable = [
        'layanan_katering_id', 'type', 'name', 'harga', 'min_qty', 'is_active', 'image', 'items',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
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

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }


    /**
     * Harga terformat
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
