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
        $todayDateString = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
        return $this->menu_date->format('Y-m-d') < $todayDateString;
    }

    /**
     * Cek apakah item ini bisa dipesan.
     * Aturan bisnis Katering Harian:
     * - Menu dapat dipesan selama tanggal menu sama dengan tanggal hari ini atau setelah hari ini.
     * - Menu dengan tanggal sebelum hari ini tidak dapat dipesan.
     * - Tidak menggunakan batas jam pemesanan (cut-off time) maupun minimal_order_days.
     */
    public function canOrder(): bool
    {
        return !$this->isPast();
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
