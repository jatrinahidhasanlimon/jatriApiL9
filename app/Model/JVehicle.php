<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JVehicle extends Model
{
    protected $fillable = ['vehicle_owner_id', 'iot_device_id', 'representation_code'];
    protected $hidden = ['vehicle_owner_id', 'iot_device_id', 'representation_code'];

    public function j_tracking_user_search_histories()
    {
        return $this->hasMany(JTrackingUserSearchHistory::class);
    }

    public function j_road()
    {
        return $this->belongsTo(JRoad::class, 'track_road_id');
    }

    public function company()
    {
        return $this->belongsTo(JVehicleCompany::class, 'vehicle_company_id');
    }
}
