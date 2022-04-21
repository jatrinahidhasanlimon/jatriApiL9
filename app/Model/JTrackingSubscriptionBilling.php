<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JTrackingSubscriptionBilling extends Model
{
    public function subscription_package()
    {
    	return $this->belongsTo(JTrackingSubscriptionPackage::class, 'subscription_package_id');
    }

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeLastSubscription(Builder $query) : Builder
    {
        return $query->whereIn('id', function (QueryBuilder $query) {
            return $query->from(static::getTable())
                ->selectRaw('max(`id`)')
                ->groupBy('user_id');
        });
    }
}
