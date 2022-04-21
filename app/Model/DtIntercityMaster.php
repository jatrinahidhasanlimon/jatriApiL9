<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class DtIntercityMaster extends Authenticatable implements JWTSubject
{

    protected $fillable = ['password', 'api_token'];
    protected $hidden = ['password', 'api_token'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function subscriptions()
    {
        return $this->hasMany(DtIntercityMasterSubscription::class, 'master_id');
    }
}
