<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MicroRideBooking extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ride()
    {
        return $this->belongsTo(MicroRide::class, 'ride_id');
    }

    public function ride_with_vehicle()
    {
        return $this->belongsTo(MicroRide::class, 'ride_id')->with('vehicle');
    }

    public function ride_with_administrator()
    {
        return $this->belongsTo(MicroRide::class, 'ride_id')->with('administrator');
    }

    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }

    public function from_stoppage()
    {
        return $this->belongsTo(JStoppage::class, 'from_stoppage_id');
    }

    public function to_stoppage()
    {
        return $this->belongsTo(JStoppage::class, 'to_stoppage_id');
    }

    public function complain()
    {
        return $this->hasOne(MicrobusComplain::class, 'booking_id');
    }

    public function feedback()
    {
        return $this->hasOne(MicrobusFeedback::class, 'booking_id');
    }
}
