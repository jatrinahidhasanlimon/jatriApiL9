<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class RentalBooking extends Model
{

    public function service_type()
    {
        return $this->belongsTo(RentalServiceType::class, 'service_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(RentalOwner::class, 'owner_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(RentalVehicle::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(RentalDriver::class, 'driver_id');
    }

}
