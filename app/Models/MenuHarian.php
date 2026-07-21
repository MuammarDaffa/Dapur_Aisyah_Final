<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuHarian extends Model
{
    use HasFactory;

    protected $table = 'menu_harian';

    protected $fillable = [
        'layanan_katering_id',
        'hari',
        'tanggal',
        'nama_menu',
        'harga',
        'stok_awal',
        'stok_tersisa',
    ];
    
    protected $casts = [
        'tanggal' => 'date'
    ];

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(MenuHarianExtra::class);
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_harian_id');
    }
}