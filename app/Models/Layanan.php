<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'tipe',
        'kapasitas_porsi_per_minggu',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'kapasitas_porsi_per_minggu' => 'integer',
        ];
    }

    // === Helpers & Scopes ===

    public function isHarian(): bool
    {
        return $this->tipe === 'harian';
    }

    public function isAcara(): bool
    {
        return $this->tipe === 'acara';
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeHarian($query)
    {
        return $query->where('tipe', 'harian');
    }

    public function scopeAcara($query)
    {
        return $query->where('tipe', 'acara');
    }

    public function getKapasitasPorsiTersisaAttribute(): int
    {
        if (!$this->isAcara() || !$this->kapasitas_porsi_per_minggu) {
            return 0; // Or null, but the type is int.
        }

        $totalPorsiPesananMingguIni = $this->pesanan()
            ->whereNotIn('status', ['dibatalkan']) // Not cancelled
            ->whereBetween('tanggal_pesanan', [
                now()->startOfWeek()->format('Y-m-d'),
                now()->endOfWeek()->format('Y-m-d')
            ])
            ->sum('porsi');

        return max(0, $this->kapasitas_porsi_per_minggu - $totalPorsiPesananMingguIni);
    }

    // === Relationships ===

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'layanan_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'layanan_id');
    }
}
