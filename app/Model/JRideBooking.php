<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class JRideBooking extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ride()
    {
        return $this->belongsTo(JRide::class, 'ride_id');
    }

    public function ride_with_vehicle()
    {
        return $this->belongsTo(JRide::class, 'ride_id')->with('vehicle');
    }

    public function from_stoppage()
    {
        return $this->belongsTo(JStoppage::class, 'from_stoppage_id');
    }

    public function to_stoppage()
    {
        return $this->belongsTo(JStoppage::class, 'to_stoppage_id');
    }
}
