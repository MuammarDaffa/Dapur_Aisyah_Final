<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuAcara extends Model
{
    use HasFactory;

    protected $table = 'menu_acara';

    protected $fillable = [
        'layanan_id',
        'nama_menu',
        'deskripsi',
        'harga_per_porsi',
        'gambar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_porsi' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function extraAcara(): HasMany
    {
        return $this->hasMany(ExtraAcara::class, 'menu_acara_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_acara_id');
    }
}
