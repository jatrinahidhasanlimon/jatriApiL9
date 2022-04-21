<?php

namespace App\Http\Controllers\Utility\Common;
use App\Http\Controllers\Controller;
use DB;
use Log;

class GMapService extends Controller
{
    private $gmap_api_key = '';
    public function __construct()
    {
        $this->gmap_api_key = config('app.google_map_api_key');
    }

    public function getLocationNameByLatLng($lat, $lng){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://maps.googleapis.com/maps/api/geocode/json?key=".$this->gmap_api_key."&latlng=".$lat.",".$lng,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $address = 'Unknown';
        $response = json_decode(curl_exec($curl), true);
        try{
            $address = $response['results'][0]['formatted_address'];
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }

        curl_close($curl);
        return $address;
    }

}
