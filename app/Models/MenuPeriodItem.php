<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPeriodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_period_id', 'product_id', 'menu_date',
    ];

    protected function casts(): array
    {
        return [
            'menu_date' => 'date',
        ];
    }

    // === Accessors ===

    /**
     * Nama hari dalam Bahasa Indonesia dari menu_date.
     * Contoh: "Senin", "Selasa"
     */
    public function getDayNameAttribute(): string
    {
        return $this->menu_date->translatedFormat('l');
    }

    /**
     * Format tanggal lengkap dengan nama hari.
     * Contoh: "Senin, 15 Juli 2026"
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->menu_date->translatedFormat('l, d F Y');
    }

    /**
     * Apakah tanggal menu sudah lewat dari hari ini?
     */
    public function isPast(): bool
    {
        return $this->menu_date->lt(Carbon::today());
    }

    /**
     * Cek apakah item ini bisa dipesan berdasarkan cutoff layanan.
     */
    public function canOrder(): bool
    {
        if ($this->isPast()) {
            return false;
        }

        // Cek cutoff dari layanan via periode
        $service = $this->menuPeriod?->cateringService;
        if (!$service || !$service->minimal_order_days) {
            // Tanpa cutoff, selama belum lewat, bisa dipesan
            return !$this->isPast();
        }

        $now = Carbon::now();
        $today = Carbon::today();
        $daysUntil = $today->diffInDays($this->menu_date, false);

        if ($daysUntil < $service->minimal_order_days) {
            return false;
        }

        if ($daysUntil == $service->minimal_order_days && $service->cutoff_time) {
            $cutoff = Carbon::createFromFormat('H:i', substr($service->cutoff_time, 0, 5));
            if ($now->format('H:i') >= $cutoff->format('H:i')) {
                return false;
            }
        }

        return true;
    }

    // === Relationships ===

    public function menuPeriod(): BelongsTo
    {
        return $this->belongsTo(MenuPeriod::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
