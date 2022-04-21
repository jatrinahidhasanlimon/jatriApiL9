<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class B2bVehicle extends Model
{
    public function company()
    {
        return $this->belongsTo(B2bCompany::class, 'company_id');
    }
}
