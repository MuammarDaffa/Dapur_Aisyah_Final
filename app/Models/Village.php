<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Village menyimpan data Kelurahan atau Desa.
 * Entitas ini melengkapi hierarki alamat pengiriman di bawah Kecamatan (District).
 */
class Village extends Model
{
    public $timestamps = false;

    protected $fillable = ['district_id', 'name'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
