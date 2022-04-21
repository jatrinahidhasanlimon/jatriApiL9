<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaybillCompany extends Model
{
    protected $hidden = ['password'];

    public function vehicles()
    {
        return $this->hasMany(WaybillVehicle::class, 'company_id')->where('status', 1);
    }
}
