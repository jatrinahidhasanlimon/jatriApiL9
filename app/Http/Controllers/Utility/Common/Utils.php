<?php

namespace App\Http\Controllers\Utility\Common;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\CacheHelper;
use App\Model\LogEvent;
use App\Model\PhoneVerification;
use Carbon\Carbon;
use DB;
use FcmPushNotification\FcmPushNotification\PushNotification;
use Log;

class Utils extends Controller
{

    public function sendPushNotification($Push){
        $push_sender = new PushNotification();
        if($Push->target_group == 'users'){
            if($Push->condition_key == 'all'){
                //$push_sender->sendToTopic('global', $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
                $tokens = DB::table('users')->whereNotNull('device_token')->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            } else if($Push->condition_key == 'mobile'){
                $tokens = DB::table('users')->where('phone', 'like', '%'.$Push->condition_value.'%')->whereNotNull('device_token')->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }else if($Push->condition_key == 'mobile_csv'){
                $tokens = DB::table('users')
                    ->whereIn('phone', $this->csvToArray($Push->condition_value))
                    ->whereNotNull('device_token')
                    ->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }
            else if ($Push->condition_key == 'division') {
                $tokens = DB::table('rental_owners')->where('division', $Push->condition_value)->whereNotNull('device_token')->pluck('device_token')->toArray();

                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }
        }else if($Push->target_group == 'rental_owners'){
            if ($Push->condition_key == 'all') {
                $push_sender->sendToTopic('RENTAL_MERCHANTS', $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            } else if ($Push->condition_key == 'mobile') {
                $tokens = DB::table('rental_owners')->where('mobile', 'like', '%' . $Push->condition_value . '%')->whereNotNull('device_token')->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }else if ($Push->condition_key == 'mobile_csv') {
                $tokens = DB::table('rental_owners')
                    ->whereIn('mobile', $this->csvToArray($Push->condition_value))
                    ->whereNotNull('device_token')
                    ->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }
            else if ($Push->condition_key == 'division') {
                $tokens = DB::table('rental_owners')->where('division', $Push->condition_value)->whereNotNull('device_token')->pluck('device_token')->toArray();

                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }
        }else if($Push->target_group == 'dt_intercity_masters'){
            if ($Push->condition_key == 'all') {
                $push_sender->sendToTopic('DIGITAL_TICKETING_AGENT', $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            } else if ($Push->condition_key == 'mobile') {
                $tokens = DB::table('dt_intercity_masters')->where('mobile', 'like', '%' . $Push->condition_value . '%')->whereNotNull('device_token')->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }else if ($Push->condition_key == 'mobile_csv') {
                $tokens = DB::table('dt_intercity_masters')
                    ->whereIn('mobile', $this->csvToArray($Push->condition_value))
                    ->whereNotNull('device_token')
                    ->pluck('device_token')->toArray();
                $push_sender->sendMultiple($tokens, $Push->title, $Push->description, $Push->image, false, json_decode($Push->payload, true));
            }
        }

    }

    public function saveLogEvent($previous_data, $updated_data, $description, $action_by){
        $logEvent = new LogEvent();
        $logEvent->previous_data = $previous_data ? json_encode($previous_data) : null;
        $logEvent->updated_data = $updated_data ? json_encode($updated_data) : null;
        $logEvent->description = $description ? json_encode($description) : null;
        $logEvent->action_by = json_encode($action_by);
        $logEvent->save();
    }

    private function csvToArray($file_url = ''){
        $string = file_get_contents($file_url, true);
        if($string === FALSE) {
            $data = [];
        } else {
            $data = preg_split("/[:,\s]+/",$string);
        }
        return $data;
    }

    public function sendOTP($phone){
        try{
            $client_ip = $this->getClientIp();
            $is_blocked = DB::table('blocked_phones')->where('phone', $phone)
                ->orWhere('ip', $client_ip)->first();
            if($is_blocked) return true;

            $is_spammed= DB::table('phone_verifications')->where('phone', $phone)
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            if($is_spammed > config('app.spam_otp_request_count')) {
                DB::table('blocked_phones')->insert([
                    'phone'         => $phone,
                    'ip'            => $client_ip,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
                return true;
            }

            $is_sent = DB::table('phone_verifications')->where('phone', $phone)
                ->orderBy('id', 'desc')
                ->where('created_at', '>', Carbon::now()->subMinute(1))
                ->first();
            if($is_sent) return true;

            $code = rand(1000, 9999);
            $message = 'Your verification code is: '.$code;

            $base_url   = config('app.sms_gateway')['base_url'];
            $sender_key = config('app.sms_gateway')['sender_key'];
            $username   = config('app.sms_gateway')['username'];
            $password   = config('app.sms_gateway')['password'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url.'?masking='.$sender_key.'&userName='.$username.'&password='.$password.'&MsgType=TEXT&receiver='.$phone.'&message='.urlencode($message),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: */*'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $sendCode = new PhoneVerification();
            $sendCode->phone = $phone;
            $sendCode->otp_code = $code;
            $sendCode->message_id = '9'.substr($phone, -3);
            $sendCode->save();
            return true;
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return false;
    }

    public function verifyOTP($phone, $code, $status = 'ACTIVE'){
        try{
            $hasCode = PhoneVerification::where('phone', $phone)
                ->where('created_at', '>=', Carbon::now()->subMinutes(10)->toDateTimeString())
                ->orderBy('id', 'desc')->first();
            if($hasCode && $hasCode->otp_code == $code && $hasCode->status == $status){
                if($hasCode->status == 'ACTIVE'){
                    $hasCode->status = 'USED';
                    $hasCode->save();
                }
                return true;
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return false;
    }

    public function sendSMS($phone, $message){
        try{
            $base_url   = config('app.sms_gateway')['base_url'];
            $sender_key = config('app.sms_gateway')['sender_key'];
            $username   = config('app.sms_gateway')['username'];
            $password   = config('app.sms_gateway')['password'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url.'?masking='.$sender_key.'&userName='.$username.'&password='.$password.'&MsgType=TEXT&receiver='.$phone.'&message='.urlencode($message),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: */*'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return true;
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return false;
    }

    public function getDivisionByLatlng($lat, $lng){

        $division_data_sets = (new CacheHelper())->getDivisionLatLngDataSets();

        $division = 'UN_IDENTIFIED';
        foreach ($division_data_sets as $division_data){
            $point = ['x' => $lat, 'y' => $lng];
            $vertices = $division_data['polygon'];
            // Check if the point sits exactly on a vertex
            foreach($vertices as $vertex) {
                if ($point == $vertex) { //in vertex
                    return $division_data['name'];
                }
            }

            // Check if the point is inside the polygon or on the boundary
            $intersections = 0;
            $vertices_count = count($vertices);

            for ($i=1; $i < $vertices_count; $i++) {
                $vertex1 = $vertices[$i-1];
                $vertex2 = $vertices[$i];
                if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                    return $division_data['name']; //in boundary
                }
                if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                    $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                    if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                        return $division_data['name']; //in boundary
                    }
                    if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                        $intersections++;
                    }
                }
            }
            // If the number of edges we passed through is odd, then it's in the polygon.
            if ($intersections % 2 != 0) {
                return $division_data['name'];
            }
        }
        return $division;
    }

    public function getClientIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }

}
