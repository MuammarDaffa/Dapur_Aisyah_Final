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
        'kapasitas_total',
        'kapasitas_tersisa',
        'minimal_porsi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'kapasitas_total' => 'integer',
            'kapasitas_tersisa' => 'integer',
            'minimal_porsi' => 'integer',
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

    // === Relationships ===

    public function menuHarian(): HasMany
    {
        return $this->hasMany(MenuHarian::class, 'layanan_id');
    }

    public function menuAcara(): HasMany
    {
        return $this->hasMany(MenuAcara::class, 'layanan_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'layanan_id');
    }
}
