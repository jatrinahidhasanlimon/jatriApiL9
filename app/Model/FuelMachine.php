<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FuelMachine extends Model
{
    public function tank()
    {
        return $this->belongsTo(FuelTank::class, 'tank_id');
    }
}
