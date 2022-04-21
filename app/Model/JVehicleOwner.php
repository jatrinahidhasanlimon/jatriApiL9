<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class JVehicleOwner extends Authenticatable implements JWTSubject
{

    protected $fillable = ['password'];
    protected $hidden = ['password', 'api_token'];

    protected $guarded = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
