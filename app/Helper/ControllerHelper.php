<?php


namespace App\Helper;

use App\Http\Controllers\Utility\Common\ThirdPartyServiceManager;
use App\Rides;
use App\User;
use DB;

class ControllerHelper
{

    public static function generateRechargeTranxID(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        return 'JR'.time().$codeAlphabet[random_int(0, 51)];
    }

    public static function generateTicketingTranxID(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        return 'JT'.time().$codeAlphabet[random_int(0, 61)];
    }

    public static function ride_validity_check($rideId, $fromCounter, $toCounter){
        $ride = Rides::where('id', $rideId)->where('status', 1)->first();
        if($ride != null) {
            if (($ride->starting_counter_id > $ride->ending_counter_id && $fromCounter > $toCounter) || ($ride->starting_counter_id < $ride->ending_counter_id && $fromCounter < $toCounter)) {
                return 1;
            }
        }
        return 0;
    }

    public static function getSuccessResponseFormat(){
        $responseData = [];
        $responseData['code'] = 200;
        $responseData['status'] = 'success';
        $responseData['message'] = 'Data successfully retrieved';
        return $responseData;
    }

    public static function getErrorResponseFormat(){
        $responseData = [];
        $responseData['code'] = 500;
        $responseData['status'] = 'error';
        $responseData['message'] = 'Something Went Wrong';
        return $responseData;
    }

    public static function getBusIdFromIOTDevice($device_id){
        try{
            $mapData = [
                '84:F3:EB:E3:A5:E9' => 1,
                '5C:CF:7F:FC:F2:3B' => 1,
            ];
            return $mapData[$device_id];
        }catch (\Exception $ex){
            return 0;
        }
    }

    public static function getRideIDFromInt($id){
        $mapData = [
            '0' => 'a', '1' => 'f', '2' => 'c', '3' => 'h', '4' => 'j', '5' => 'm', '6' => 's', '7' => 'z', '8' => 'w', '9' => 'q',
        ];
        $rideIdChars = '';
        $rideIdArray = str_split($id.'');
        foreach ($rideIdArray as $char) {
            $rideIdChars = $rideIdChars.$mapData[$char];
        }
        return $rideIdChars;
    }

    public static function generateTicketBookingID(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        return 'T'.time().$codeAlphabet[random_int(0, 61)];
    }

    public static function generateRideID(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $id = '';
        for($i=1; $i<=6; $i++){
            $id = $id.$codeAlphabet[random_int(0, 35)];
        }
        return $id;
    }

    public static function generateUserRefCode(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = '';
        for($i=1; $i<=6; $i++){
            $code = $code.$codeAlphabet[random_int(0, 35)];
        }
        $check = User::where('my_ref_code', $code)->first();
        if($check == null){
            return $code;
        }
        ControllerHelper::generateUserRefCode();
    }

    public static function generateRentalMerchantRefCode($length = 6){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = '';
        for($i=1; $i<=$length; $i++){
            $code = $code.$codeAlphabet[random_int(0, 35)];
        }
        $check = DB::table('rental_owners')->where('my_ref_code', $code)->first();
        if($check == null){
            return $code;
        }
        ControllerHelper::generateRentalMerchantRefCode();
    }

    public static function getInvoicePostFix(){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = '';
        for($i=1; $i<=3; $i++){
            $code = $code.$codeAlphabet[random_int(0, 35)];
        }
        return $code;
    }

    public static function getBookingPostFix($lenght = 1){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = '';
        for($i = 1; $i <= $lenght; $i++){
            $code = $code.$codeAlphabet[random_int(0, 35)];
        }
        return $code;
    }

    public static function identifyVehicles($vehicles, $isSequential, $stoppage_sequence, $pickStoppage, $dropStoppage){
        $final_vehicle_list = [];
        foreach ($vehicles as $vehicle){
            // detect available vehicle -- start
            $pick_stoppage_key = array_search($pickStoppage->id, $stoppage_sequence);
            $vehicle_stoppage_key = array_search($vehicle->last_stoppage_id, $stoppage_sequence);
            if( $isSequential && $pick_stoppage_key > $vehicle_stoppage_key ){
                $googleData = getDataFromMapAPI($vehicle->current_latitude, $vehicle->current_longitude, $pickStoppage->latitude, $pickStoppage->longitude);
                if($googleData['distance'] > 10.0) continue;
                $vehicle->google_map_distance = $googleData['distance'];
                $vehicle->google_map_duration = $googleData['duration'];
                $vehicle->pick_stoppage_latitude = $pickStoppage->latitude;
                $vehicle->pick_stoppage_longitude = $pickStoppage->longitude;
                $vehicle->drop_stoppage_latitude = $dropStoppage->latitude;
                $vehicle->drop_stoppage_longitude = $dropStoppage->longitude;
                $final_vehicle_list[] = $vehicle;
            }else if( !$isSequential && $pick_stoppage_key < $vehicle_stoppage_key ){
                $googleData = getDataFromMapAPI($vehicle->current_latitude, $vehicle->current_longitude, $pickStoppage->latitude_alt, $pickStoppage->longitude_alt);
                if($googleData['distance'] > 10.0) continue;
                $vehicle->google_map_distance = $googleData['distance'];
                $vehicle->google_map_duration = $googleData['duration'];
                $vehicle->pick_stoppage_latitude = $pickStoppage->latitude_alt;
                $vehicle->pick_stoppage_longitude = $pickStoppage->longitude_alt;
                $vehicle->drop_stoppage_latitude = $dropStoppage->latitude_alt;
                $vehicle->drop_stoppage_longitude = $dropStoppage->longitude_alt;
                $final_vehicle_list[] = $vehicle;
            }// detect available vehicle -- start
        }
        return $final_vehicle_list;
    }

    public static function hasOwnerSubscription($owner_id){
        $hasSubscription = DB::table('j_vehicle_owner_subscription_billings')->where('vehicle_owner_id', $owner_id)->where('to_date', '>=', date('Y-m-d'))->orderBy('id', 'desc')->first();
        if($hasSubscription != null && $hasSubscription->status == 1){
            $subscribed_ids = json_decode($hasSubscription->allowed_vehicles_id, true);
            return $subscribed_ids;
        }
        return false;
    }

    public function getChargeForBazar($service_type, $total_price, $user_id, $sub_count=0){
        try{
            $charge = []; $charge['delivery_charge'] = 50; $charge['service_charge_percentage'] = 0;
            $charge['discount'] = 0;
            $home_content = DB::table('settings')->where('key', 'order_meta_data_v2')->first();
            if($home_content){
                $home_content = json_decode($home_content->value);
                foreach ($home_content->services as $service){
                    if($service->name == $service_type){
                        $charge['delivery_charge'] = $service->delivery_charge;
                        $charge['service_charge_percentage'] = $service->service_charge_percentage;

                        if($service->discount_offer->status || $service->free_delivery_offer->status){
                            $user_order_count = DB::table('orders')->where('user_id', $user_id)
                                    ->whereIn('status', ['PENDING', 'PROCESSING', 'ACCEPTED', 'SHIPPING', 'DELIVERED'])
                                    ->count() - $sub_count;

                            if($service->discount_offer->status && $user_order_count < $service->discount_offer->order_count){
                                $discount = round(($service->discount_offer->discount_percentage / 100) * $total_price);
                                if($discount > $service->discount_offer->express->upto){
                                    $discount = $service->discount_offer->express->upto;
                                }
                                $charge['discount'] = $discount;
                            }
                            if($service->free_delivery_offer->status && $user_order_count < $service->free_delivery_offer->order_count){
                                $charge['delivery_charge'] = 0;
                            }
                        }
                        break;
                    }
                }
            }

            return $charge;
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToGoogleCloud(
                $ex->getMessage(), $ex->getFile(), $ex->getLine(), 'getChargeForBazar'
            );
        }
        return $charge;
    }

    public static function getDatesFromRange($a,$b,$x=0,$dates=[]){
        while(end($dates)!=$b && $x=array_push($dates,date("Y-m-d",strtotime("$a +$x day"))));
        return $dates;
    }
}
