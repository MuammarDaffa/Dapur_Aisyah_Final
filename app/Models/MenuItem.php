<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_item';

    protected $fillable = [
        'menu_id',
        'nama',
        'harga',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
