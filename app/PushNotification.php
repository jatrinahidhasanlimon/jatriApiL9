<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
