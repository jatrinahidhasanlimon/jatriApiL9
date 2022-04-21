<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RechargeHistory extends Model
{
    protected $table = "recharge_history";
    protected $primaryKey = 'id';
    public $timestamps = false;
   
   public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
