<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JRide extends Model
{
    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }
}
