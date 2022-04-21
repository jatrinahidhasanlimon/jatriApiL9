<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RechargeBonus extends Model
{
    protected $table = 'recharge_bonus';
    protected $fillable = ['amount'];
}
