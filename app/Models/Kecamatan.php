<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Kecamatan merepresentasikan entitas Kecamatan.
 * Berfungsi sebagai relasi wilayah (Desa) dan basis perhitungan tarif pengiriman (OngkosKirim).
 */
class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function desa(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    public function ongkosKirim(): HasMany
    {
        return $this->hasMany(OngkosKirim::class);
    }
}
