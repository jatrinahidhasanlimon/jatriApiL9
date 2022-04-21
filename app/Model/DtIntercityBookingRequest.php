<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DtIntercityBookingRequest extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(DtIntercityBookingService::class, 'request_id');
    }

    public function priority_one_requested_service()
    {
        return $this->belongsTo(DtIntercityBookingService::class, 'id', 'request_id')
            ->where('priority', 1)
            ->with('service_with_company','boarding');
    }

    public function services_with_details()
    {
        return $this->hasMany(DtIntercityBookingService::class, 'request_id')
            ->with('service_with_company','boarding');
    }

    public function master()
    {
        return $this->belongsTo(DtIntercityMaster::class, 'master_id');
    }

    public function accepted_service()
    {
        return $this->belongsTo(DtIntercityBookingService::class, 'service_id')->with('service_with_company','boarding');
    }

}
