<?php

namespace App;

use App\Model\JTrackingSubscriptionBilling;
use App\Model\JTrackingUserSearchHistory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $fillable = ['id', 'phone', 'first_name', 'last_name', 'password', 'address', 'city', 'email', 'gender', 'date_of_birth', 'image', 'balance', 'tracking_active_to_date', 'tracking_status', 'role_id', 'api_token', 'device_token'];
    protected $hidden = ['password', 'api_token'];

    public function scopeExclude($query,$value = array())
    {
        return $query->select( array_diff( $this->fillable,(array) $value) );
    }

	  public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function j_tracking_user_search_histories()
    {
        return $this->hasMany(JTrackingUserSearchHistory::class);
    }

    public function j_tracking_subscription_billings()
    {
        return $this->hasMany(JTrackingSubscriptionBilling::class);
    }

    public function scopeMicroPickUsers($query, $ride_id, $stoppage_id){
        $pick_booking_users = DB::table('micro_ride_bookings')->where('ride_id', $ride_id)->where('from_stoppage_id', $stoppage_id)->where('status', 'PAID')->pluck('user_id');
        return $query->select('id', 'first_name', 'last_name', 'phone', 'image')->whereIn('id', $pick_booking_users);
    }

    public function scopeMicroDropUsers($query, $ride_id, $stoppage_id){
        $drop_booking_users = DB::table('micro_ride_bookings')->where('ride_id', $ride_id)->where('to_stoppage_id', $stoppage_id)->where('status', 'CONFIRMED')->pluck('user_id');
        return $query->select('id', 'first_name', 'last_name', 'phone', 'image')->whereIn('id', $drop_booking_users);
    }
}
