<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IsiMenu extends Model
{
    use HasFactory;

    protected $table = 'isi_menu';

    protected $fillable = [
        'menu_acara_id',
        'nama',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function menuAcara(): BelongsTo
    {
        return $this->belongsTo(MenuAcara::class, 'menu_acara_id');
    }
}
