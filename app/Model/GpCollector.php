<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class GpCollector extends Authenticatable implements JWTSubject
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
}
