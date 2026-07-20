<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model PeriodeMenu merepresentasikan periode jadwal untuk layanan Katering Harian.
 * Berfungsi mengelompokkan menu-menu harian (ItemPeriodeMenu) ke dalam rentang tanggal tertentu.
 */
class PeriodeMenu extends Model
{
    protected $table = 'periode_menu';

    use HasFactory;

    protected $fillable = [
        'layanan_katering_id', 'start_date', 'end_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // === Accessors ===

    /**
     * Formatted range tanggal.
     */
    /**
         * Mendapatkan format pembacaan rentang tanggal periode secara manusiawi.
         * Contoh Output: 01 Jul 2026 — 07 Jul 2026
         * @return string
         */
    public function getFormattedRangeAttribute(): string
    {
        return $this->start_date->translatedFormat('d M Y') . ' — ' . $this->end_date->translatedFormat('d M Y');
    }

    // === Relationships ===

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPeriodeMenu::class)->orderBy('menu_date');
    }
}
