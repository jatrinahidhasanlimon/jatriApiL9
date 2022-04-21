<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DtIntercityBookingService extends Model
{
    public function service()
    {
        return $this->belongsTo(DtIntercityService::class, 'service_id');
    }

    public function service_with_company()
    {
        return $this->belongsTo(DtIntercityService::class, 'service_id')->with('company');
    }

    public function boarding()
    {
        return $this->belongsTo(DtIntercityBoardingPoint::class, 'boarding_id');
    }

    public function schedule()
    {
        return $this->belongsTo(DtIntercityTimeSchedule::class, 'schedule_id');
    }
}
