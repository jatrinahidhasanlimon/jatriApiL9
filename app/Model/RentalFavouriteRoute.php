<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RentalFavouriteRoute extends Model
{
    public function service_type()
    {
        return $this->belongsTo(RentalServiceType::class, 'service_type_id');
    }
}
