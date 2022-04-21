<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserComplain extends Model
{
    public function vehicle()
    {
        return $this->belongsTo(JVehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
