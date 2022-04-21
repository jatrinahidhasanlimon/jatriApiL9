<?php

namespace App\Http\Controllers\Utility\Common;
use App\Helper\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Model\JRide;
use DB;
use FcmPushNotification\FcmPushNotification\PushNotification;
use Log;

class LocationUpdateAction extends Controller
{

    public static function checkStoppage($vehicle, $distance){

        try{
            $road_sequence = json_decode($vehicle->j_road->sequence, true);

            /*
             * This uses the ‘haversine’ formula to calculate the great-circle distance between two points – that is,
             * the shortest distance over the earth’s surface – giving an ‘as-the-crow-flies’
             * distance between the points (ignoring any hills they fly over, of course!).
             * */
            $stoppages = DB::table('j_stoppages')->select(DB::Raw("(6371008 * acos( cos( radians('$vehicle->current_latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$vehicle->current_longitude') ) + sin( radians('$vehicle->current_latitude') ) * sin( radians(latitude) ) ) ) AS distance"),'id')
                ->whereIn('id', $road_sequence)
                ->orderBy('distance','asc')
                ->first();

            if($stoppages->distance < 100 && $vehicle->last_stoppage_id != $stoppages->id){
                $new_index = array_search($stoppages->id, $road_sequence);
                $old_index = array_search($vehicle->last_stoppage_id, $road_sequence);
                if($new_index == 0 || ($new_index > $old_index && $new_index != (count($road_sequence) - 1))){
                    $vehicle->ride_direction_sequential = true;
                }else if($new_index == (count($road_sequence) - 1) || $new_index < $old_index){
                    $vehicle->ride_direction_sequential = false;
                }
                $vehicle->last_stoppage_id = $stoppages->id;
                $vehicle->save();

                LocationUpdateAction::sendPushPreviousStoppageAlert($vehicle);
                LocationUpdateAction::sendPushPickStoppageAlert($vehicle);

                LocationUpdateAction::addStoppageHitStat($vehicle, $distance);

                if( $vehicle->booking_availability == 1 && ($new_index == 0 || $new_index == (count($road_sequence) - 1))){
                    LocationUpdateAction::createNewRide($vehicle);
                }
            }else{
                if(DB::table('vehicle_location_stats')->where('vehicle_id', $vehicle->id)->where('created_at', date('Y-m-d H:i').':00')->first() == null){
                    DB::table('vehicle_location_stats')->insert(
                        [
                            'vehicle_id' => $vehicle->id,
                            'latitude' => $vehicle->current_latitude,
                            'longitude' => $vehicle->current_longitude,
                            'stoppage_id' => 0,
                            'distance' => $distance,
                            'created_at' => date('Y-m-d H:i').':00'
                        ]
                    );
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }

    }

    private static function addStoppageHitStat($vehicle, $distance){
        DB::table('vehicle_location_stats')->insert(
            [
                'vehicle_id' => $vehicle->id,
                'latitude' => $vehicle->current_latitude,
                'longitude' => $vehicle->current_longitude,
                'stoppage_id' => $vehicle->last_stoppage_id,
                'distance' => $distance,
                'created_at' => date('Y-m-d H:i').':00'
            ]
        );
    }

    private static function sendPushPreviousStoppageAlert($vehicle){
        $alertOns = DB::table('j_tracking_alert_notifications')->where('vehicle_id', $vehicle->id)
            ->where('notification', true)
            ->where('previous_stoppage_notification_done', false)
            ->where('pick_stoppage_notification_done', false)
            ->where('previous_stoppage_id', $vehicle->last_stoppage_id)
            ->get();

        if(count($alertOns) > 0){
            $userIds = []; $trackingIds = [];
            foreach ($alertOns as $item){
                $userIds[] = $item->user_id;
                $trackingIds[] = $item->id;
            }
            $vehicleStoppage = DB::table('j_stoppages')->where('id', $vehicle->last_stoppage_id)->first();
            DB::table('j_tracking_alert_notifications')->whereIn('id', $trackingIds)->update(['previous_stoppage_notification_done' => true]);
            $deviceTokens = DB::table('users')->whereIn('id', $userIds)->pluck('device_token')->toArray();
            (new PushNotification())->sendMultiple($deviceTokens, 'Ready to go?', 'Bus no '.$vehicle->registration_number.' just reached '.$vehicleStoppage->name, '', false, '');
        }
    }

    private static function sendPushPickStoppageAlert($vehicle){
        $alertOns = DB::table('j_tracking_alert_notifications')->where('vehicle_id', $vehicle->id)
            ->where('notification', true)
            ->where('pick_stoppage_notification_done', false)
            ->where('pick_stoppage_id', $vehicle->last_stoppage_id)
            ->get();

        if(count($alertOns) > 0){
            $userIds = []; $trackingIds = [];
            foreach ($alertOns as $item){
                $userIds[] = $item->user_id;
                $trackingIds[] = $item->id;
            }
            DB::table('j_tracking_alert_notifications')->whereIn('id', $trackingIds)->update(['pick_stoppage_notification_done' => true]);
            $deviceTokens = DB::table('users')->whereIn('id', $userIds)->pluck('device_token')->toArray();
            (new PushNotification())->sendMultiple($deviceTokens, 'Your bus is here', 'Bus no '.$vehicle->registration_number.' has reached you. Enjoy your trip!', '', false, '');
        }
    }

    private static function createNewRide($vehicle){
        DB::table('j_rides')->where('vehicle_id', $vehicle->id)->where('status', 1)->update(['status' => 0]);

        //create new
        $ride = new JRide();
        $ride->vehicle_id = $vehicle->id;
        $ride->ride_direction_sequential = $vehicle->ride_direction_sequential;
        $ride->ride_id = ControllerHelper::generateRideID();
        $ride->save();
    }

}
