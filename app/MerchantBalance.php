<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantBalance extends Model
{
    protected $table = "merchant_balance";
    protected $primaryKey = 'id';
    public $timestamps = true;
}
