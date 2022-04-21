<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BuyPass extends Model
{
    public function stoppage_one()
    {
        return $this->belongsTo(JStoppage::class, 'stoppage_id_one');
    }

    public function stoppage_two()
    {
        return $this->belongsTo(JStoppage::class, 'stoppage_id_two');
    }

    public function company()
    {
        return $this->belongsTo(JVehicleCompany::class, 'company_id');
    }
}
