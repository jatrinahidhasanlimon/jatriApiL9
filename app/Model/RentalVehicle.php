<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RentalVehicle extends Model
{
    public function service_type()
    {
        return $this->belongsTo(RentalServiceType::class, 'service_type_id');
    }
}
