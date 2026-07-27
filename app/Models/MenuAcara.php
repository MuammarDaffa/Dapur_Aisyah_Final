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
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function isiMenu(): HasMany
    {
        return $this->hasMany(IsiMenu::class, 'menu_acara_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_acara_id');
    }
}
