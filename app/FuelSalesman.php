<?php

namespace App;

use App\Model\FuelCompany;
use App\Model\FuelMachine;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class FuelSalesman extends Authenticatable implements JWTSubject
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

    public function company()
    {
        return $this->belongsTo(FuelCompany::class, 'company_id');
    }

    public function machine()
    {
        return $this->belongsTo(FuelMachine::class, 'machine_id');
    }

    public function machine_with_tank()
    {
        return $this->belongsTo(FuelMachine::class, 'machine_id')->with('tank');
    }
}
