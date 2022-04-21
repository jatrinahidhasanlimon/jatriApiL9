<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class JTrackingUserSearchHistory extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pick()
    {
        return $this->belongsTo(JStoppage::class, 'pick_stoppage_id');
    }

    public function drop()
    {
        return $this->belongsTo(JStoppage::class, 'drop_stoppage_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }
}
