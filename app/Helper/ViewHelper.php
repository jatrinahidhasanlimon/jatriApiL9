<?php

use App\Http\Controllers\Utility\CacheHelper;
use App\Http\Controllers\Utility\Common\ThirdPartyServiceManager;
//use Log;

function getDataFromMapAPI($from_lat, $from_long, $to_lat, $to_long){
    try{
        $map_keys = (new CacheHelper())->getGoogleMapAPI();
        $googleMapReq = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $from_lat . "," . $from_long . "&destinations=" . $to_lat . "," . $to_long . "&departure_time=now&key=" .$map_keys['distance_metrics'];
        $details = json_decode(curl($googleMapReq), TRUE);
        $kilometer = round($details['rows'][0]['elements'][0]['distance']['value'] / 1000, 2);
        $minutes = round($details['rows'][0]['elements'][0]['duration_in_traffic']['value'] / 60);
        return [
            'distance' => $kilometer,
            'duration' => $minutes
        ];
    }catch (\Exception $ex){
        ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        return [
            'distance' => 0,
            'duration' => 0
        ];
    }
}

function getRentalDistanceFromMapAPI($pick_latitude, $pick_longitude, $via_latitude, $via_longitude, $drop_latitude, $drop_longitude){
    try{
        if($via_latitude > 0 && $via_longitude > 0){
            $first_half = getDistanceFromMap($pick_latitude, $pick_longitude, $via_latitude, $via_longitude);
            $second_half = getDistanceFromMap($via_latitude, $via_longitude, $drop_latitude, $drop_longitude);
            return $first_half + $second_half;
        }
        return getDistanceFromMap($pick_latitude, $pick_longitude, $drop_latitude, $drop_longitude);
    }catch (\Exception $ex){
        ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        return 0;
    }
}

function getDistanceFromMap($from_lat, $from_long, $to_lat, $to_long){
    try{
        $map_keys = (new CacheHelper())->getGoogleMapAPI();
        $googleMapReq = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $from_lat . "," . $from_long . "&destinations=" . $to_lat . "," . $to_long . "&key=" .$map_keys['distance_metrics'];
        $details = json_decode(curl($googleMapReq), TRUE);
        return round($details['rows'][0]['elements'][0]['distance']['value'] / 1000, 2);
    }catch (\Exception $ex){
        ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        return 0;
    }
}

function getRentalDistanceWithTimeFromMapAPI($pick_latitude, $pick_longitude, $via_latitude, $via_longitude, $drop_latitude, $drop_longitude){
    try{
        if($via_latitude > 0 && $via_longitude > 0){
            $first_half_res = getDistanceWithTimeFromMap($pick_latitude, $pick_longitude, $via_latitude, $via_longitude);
            $first_half_distance = $first_half_res['distance'];
            $first_half_duration = $first_half_res['duration'];
            $second_half_res = getDistanceWithTimeFromMap($via_latitude, $via_longitude, $drop_latitude, $drop_longitude);
            $second_half_distance = $second_half_res['distance'];
            $second_half_duration = $second_half_res['duration'];
            return [
                'distance' => $first_half_distance + $second_half_distance,
                'duration' => $first_half_duration + $second_half_duration,
            ];
        }
        return getDistanceWithTimeFromMap($pick_latitude, $pick_longitude, $drop_latitude, $drop_longitude);
    }catch (\Exception $ex){
        ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        return [
            'distance' => 0,
            'duration' => 0,
        ];
    }
}

function getDistanceWithTimeFromMap($from_lat, $from_long, $to_lat, $to_long){
    try{
        $map_keys = (new CacheHelper())->getGoogleMapAPI();
        $googleMapReq = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $from_lat . "," . $from_long . "&destinations=" . $to_lat . "," . $to_long . "&key=" .$map_keys['distance_metrics'];
        $details = json_decode(curl($googleMapReq), TRUE);
        $distance = round($details['rows'][0]['elements'][0]['distance']['value'] / 1000, 2);
        $duration = round($details['rows'][0]['elements'][0]['duration']['value'] / 60);
        return [
            'distance' => $distance,
            'duration' => $duration,
        ];
    }catch (\Exception $ex){
        Log::error('getDistanceWithTimeFromMap => '. $googleMapReq);
        ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        return [
            'distance' => 0,
            'duration' => 0,
        ];
    }
}

function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);
    curl_close ($ch);
    return $return;
}

function get_phone_by_removing_obstacles($phone)
{
    return substr($phone, -11);
}

function get_phone_by_adding_country_code($phone)
{
    return "+880".substr($phone, -10);
}
