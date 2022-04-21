<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JTrackingAlertNotification extends Model
{
    public function history()
    {
        return $this->belongsTo(JTrackingUserSearchHistory::class, 'tracking_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }
}
