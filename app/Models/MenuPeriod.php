<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'catering_service_id', 'nama_periode', 'start_date', 'end_date', 'is_active',
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

    /**
     * Periode yang mencakup tanggal hari ini.
     */
    public function scopeCurrent($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    /**
     * Periode setelah periode aktif (start_date > today).
     */
    public function scopeUpcoming($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '>', $today);
    }

    // === Accessors ===

    /**
     * Apakah periode ini mencakup hari ini?
     */
    public function isCurrent(): bool
    {
        $today = Carbon::today();
        return $this->start_date->lte($today) && $this->end_date->gte($today);
    }

    /**
     * Formatted range tanggal.
     */
    public function getFormattedRangeAttribute(): string
    {
        return $this->start_date->translatedFormat('d M Y') . ' — ' . $this->end_date->translatedFormat('d M Y');
    }

    // === Relationships ===

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuPeriodItem::class)->orderBy('menu_date');
    }
}
