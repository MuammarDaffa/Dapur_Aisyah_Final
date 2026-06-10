<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function shippingCosts(): HasMany
    {
        return $this->hasMany(ShippingCost::class);
    }
}
