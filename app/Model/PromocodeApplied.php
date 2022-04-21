<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PromocodeApplied extends Model
{
    public function promocode()
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }
}
