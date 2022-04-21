<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DtIntercityFare extends Model
{
    public function service()
    {
        return $this->belongsTo(DtIntercityService::class,'service_id');
    }

    public function service_with_company_with_boardings_with_schedules()
    {
        return $this->belongsTo(DtIntercityService::class,'service_id')->with('company','boardings','schedules');
    }
}
