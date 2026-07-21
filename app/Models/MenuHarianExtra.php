<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuHarianExtra extends Model
{
    use HasFactory;

    protected $table = 'menu_harian_extra';

    protected $fillable = [
        'menu_harian_id',
        'nama_extra',
        'harga',
    ];

    public function menuHarian(): BelongsTo
    {
        return $this->belongsTo(MenuHarian::class);
    }
}