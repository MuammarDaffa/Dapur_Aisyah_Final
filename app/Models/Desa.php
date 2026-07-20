<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Desa menyimpan data Kelurahan atau Desa.
 * Entitas ini melengkapi hierarki alamat pengiriman di bawah Kecamatan (Kecamatan).
 */
class Desa extends Model
{
    protected $table = 'desa';

    public $timestamps = false;

    protected $fillable = ['kecamatan_id', 'name'];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
