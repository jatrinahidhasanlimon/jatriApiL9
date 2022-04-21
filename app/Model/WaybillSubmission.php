<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaybillSubmission extends Model
{
    public function vehicle()
    {
        return $this->belongsTo(WaybillVehicle::class, 'vehicle_id');
    }

    public function checker()
    {
        return $this->belongsTo(WaybillChecker::class, 'checker_id');
    }

    public function check_point()
    {
        return $this->belongsTo(WaybillCheckPoint::class, 'check_point_id');
    }
}
