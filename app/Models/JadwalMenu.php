<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalMenu extends Model
{
    use HasFactory;

    protected $table = 'jadwal_menu';

    protected $fillable = [
        'menu_harian_id',
        'hari',
        'aktif',
        'tanggal',
        'stok_awal',
        'stok_tersisa',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'tanggal' => 'date',
            'stok_awal' => 'integer',
            'stok_tersisa' => 'integer',
        ];
    }

    public function menuHarian(): BelongsTo
    {
        return $this->belongsTo(MenuHarian::class, 'menu_harian_id');
    }

    public function extraHarian(): HasMany
    {
        return $this->hasMany(ExtraHarian::class, 'jadwal_menu_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'jadwal_menu_id');
    }
}
