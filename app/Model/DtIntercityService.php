<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DtIntercityService extends Model
{
    public function boardings()
    {
        return $this->hasMany(DtIntercityBoardingPoint::class, 'service_id')->where('status', 1);
    }

    public function schedules()
    {
        return $this->hasMany(DtIntercityTimeSchedule::class, 'service_id');
    }

    public function company()
    {
        return $this->belongsTo(DtIntercityCompany::class);
    }

}
