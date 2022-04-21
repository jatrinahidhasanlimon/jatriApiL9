<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RentalBookForEvent extends Model
{
    protected $fillable = ['status'];
    public function service_type()
    {
        return $this->belongsTo(RentalServiceType::class, 'service_type_id');
    }
}
