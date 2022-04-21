<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Merchant extends Authenticatable implements JWTSubject
{
    protected $table = "merchant";
    protected $primaryKey = 'id';
    protected $hidden = ['password', 'api_token'];
    public $timestamps = true;

    public function rechargeHistory() {
        return $this->hasMany(RechargeHistory::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
