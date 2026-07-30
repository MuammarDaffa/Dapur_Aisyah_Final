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
        'status',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
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

    public function minumans(): HasMany
    {
        return $this->hasMany(Minuman::class);
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

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'layanan_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'layanan_id');
    }
}
