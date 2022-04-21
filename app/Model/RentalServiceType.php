<?php

namespace App\Model;
use  App\Model\RentalBookForEvent;

use Illuminate\Database\Eloquent\Model;

class RentalServiceType extends Model
{
    public function events()
    {
        return $this->hasMany(RentalBookForEvent::class,'service_type_id');
    }
}
