<?php

namespace App\Model;

use App\MicrobusAdministrator;
use Illuminate\Database\Eloquent\Model;

class MicroRide extends Model
{
    public function road()
    {
        return $this->belongsTo(JRoad::class, 'road_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }

    public function administrator()
    {
        return $this->belongsTo(MicrobusAdministrator::class, 'administrator_id');
    }

    public function bookings(){
        return $this->hasMany(MicroRideBooking::class, 'ride_id')->whereIn('status', ['BOOKED', 'PAID', 'CONFIRMED']);
    }

}
