<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DtIntercityBookingRequestFilter extends Model
{
    public function booking_service()
    {
        return $this->belongsTo(DtIntercityBookingService::class,'booking_service_id');
    }

    public function booking_service_with_details()
    {
        return $this->belongsTo(DtIntercityBookingService::class,'booking_service_id')->with('service_with_company', 'boarding');
    }

    public function booking()
    {
        return $this->belongsTo(DtIntercityBookingRequest::class,'request_id');
    }
}
