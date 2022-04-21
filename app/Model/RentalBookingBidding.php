<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RentalBookingBidding extends Model
{
    public function owner()
    {
        return $this->belongsTo(RentalOwner::class, 'owner_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(RentalVehicle::class, 'vehicle_id');
    }

    public function booking()
    {
        return $this->belongsTo(RentalBooking::class, 'booking_id');
    }

    public function booking_with_service_type()
    {
        return $this->belongsTo(RentalBooking::class, 'booking_id')->with('service_type');
    }
}
