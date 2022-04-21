<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JStoppage extends Model
{
    public function j_tracking_user_search_histories()
    {
        return $this->hasMany(JTrackingUserSearchHistory::class);
    }
}
