<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JTrackingSubscriptionPackage extends Model
{
    protected $fillable = ['status'];

    public function j_tracking_subscription_billings()
    {
    	return $this->hasMany(JTrackingSubscriptionBilling::class);
    }
}
