<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraHarian extends Model
{
    use HasFactory;

    protected $table = 'extra_harian';

    protected $fillable = [
        'jadwal_menu_id',
        'nama',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function jadwalMenu(): BelongsTo
    {
        return $this->belongsTo(JadwalMenu::class, 'jadwal_menu_id');
    }
}
