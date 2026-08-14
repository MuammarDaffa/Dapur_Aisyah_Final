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
        'menu_id',
        'hari',
        'tanggal',
        'stok_awal',
        'stok_tersisa',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'stok_awal' => 'integer',
            'stok_tersisa' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
