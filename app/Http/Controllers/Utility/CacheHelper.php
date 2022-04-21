<?php

namespace App\Http\Controllers\Utility;
use App\Http\Controllers\Controller;
use App\Model\BusExpressAgentCompanySubscription;
use App\Model\BusExpressBookingBidding;
use App\Model\BusExpressBookingRequest;
use App\Model\BusExpressCompany;
use App\Model\DtIntercityBookingRequestFilter;
use App\Model\Promocode;
use App\Model\RentalBooking;
use App\Model\RentalBookingBidding;
use App\Model\RentalFavouriteRoute;
use App\Model\RentalServiceType;
use App\Model\RentalVehicle;
use DB;
use Log;
use Cache;

class CacheHelper extends Controller
{
    const CACHE_TIME        = 3600;  //unit -> second
    const CACHE_TIME_10_MIN = 600;  //unit -> second
    const CACHE_TIME_6H     = 21600; //unit -> second
    const CACHE_TIME_8H     = 28800; //unit -> second
    const CACHE_TIME_12H    = 43200; //unit -> second
    const CACHE_TIME_24H    = 86400; //unit -> second
    const CACHE_TIME_240H    = 864000; //unit -> second

    const TRACKING_TICKETING_COMPANY_CACHE_KEY                      = 'TRACKING_TICKETING_COMPANY_CACHE_KEY';
    const SUPERVISOR_TICKETING_COMPANY_CACHE_KEY                    = 'SUPERVISOR_TICKETING_COMPANY_CACHE_KEY';
    const SUPERVISOR_COUNTERMEN_CACHE_KEY                           = 'SUPERVISOR_COUNTERMEN_CACHE_KEY';
    const TRACKING_TICKETING_VEHICLE_CACHE_KEY                      = 'TRACKING_TICKETING_VEHICLE_CACHE_KEY';
    const TRACKING_TICKETING_ROAD_CACHE_KEY                         = 'TRACKING_TICKETING_ROAD_CACHE_KEY';
    const TICKETING_FARE_MODALITY_CACHE_KEY                         = 'TICKETING_FARE_MODALITY_CACHE_KEY';
    const TOLL_PLAZA_COMPANY_CACHE_KEY                              = 'TOLL_PLAZA_COMPANY_CACHE_KEY';
    const TOLL_PLAZA_COLLECTORS_CACHE_KEY                           = 'TOLL_PLAZA_COLLECTORS_CACHE_KEY';
    const FUEL_SERVICE_COMPANY_CACHE_KEY                            = 'FUEL_SERVICE_COMPANY_CACHE_KEY';
    const FUEL_SERVICE_SALESMAN_CACHE_KEY                           = 'FUEL_SERVICE_SALESMAN_CACHE_KEY';
    const FUEL_SERVICE_MACHINE_CACHE_KEY                            = 'FUEL_SERVICE_MACHINE_CACHE_KEY';
    const FUEL_SERVICE_TANK_CACHE_KEY                               = 'FUEL_SERVICE_TANK_CACHE_KEY';
    const GP_SERVICE_COMPANY_CACHE_KEY                              = 'GP_SERVICE_COMPANY_CACHE_KEY';
    const GP_SERVICE_VEHICLE_CACHE_KEY                              = 'GP_SERVICE_VEHICLE_CACHE_KEY';
    const RENTAL_AVAILABLE_SERVICE_CACHE_KEY                        = 'RENTAL_AVAILABLE_SERVICE_CACHE_KEY';
    const RENTAL_ALL_SERVICE_CACHE_KEY                              = 'RENTAL_ALL_SERVICE_CACHE_KEY';
    const RENTAL_SHUTDOWN_REQUEST_CACHE_KEY                         = 'RENTAL_SHUTDOWN_REQUEST_CACHE_KEY';
    const RENTAL_OWNER_REGISTRATION_BONUS_CREDIT_CACHE_KEY          = 'RENTAL_OWNER_REGISTRATION_BONUS_CREDIT_CACHE_KEY';
    const RENTAL_PENDING_BOOKING_REQUESTS_CACHE_KEY                 = 'RENTAL_PENDING_BOOKING_REQUESTS_CACHE_KEY';
    const RENTAL_WAITING_BOOKING_REQUESTS_CACHE_KEY                 = 'RENTAL_WAITING_BOOKING_REQUESTS_CACHE_KEY';
    const RENTAL_BIDDINGS_CACHE_KEY                                 = 'RENTAL_BIDDINGS_CACHE_KEY';
    const RENTAL_PROMOCODE_CACHE_KEY                                = 'RENTAL_PROMOCODE_CACHE_KEY';
    const RENTAL_MAX_BIDDINGS_CACHE_KEY                             = 'RENTAL_MAX_BIDDINGS_CACHE_KEY';
    const RENTAL_OWNER_BIDDINGS_CACHE_KEY                           = 'RENTAL_OWNER_BIDDINGS_CACHE_KEY';
    const RENTAL_OWNER_VEHICLE_CACHE_KEY                            = 'RENTAL_OWNER_VEHICLE_CACHE_KEY';
    const RENTAL_OWNER_DRIVER_CACHE_KEY                             = 'RENTAL_OWNER_DRIVER_CACHE_KEY';
    const RENTAL_FAVOURITE_ROUTE_CACHE_KEY                          = 'RENTAL_FAVOURITE_ROUTE_CACHE_KEY';
    const RENTAL_BOOK_FOR_EVENT_CACHE_KEY                           = 'RENTAL_BOOK_FOR_EVENT_CACHE_KEY';
    const GOOGLE_MAP_API_CACHE_KEY                                  = 'GOOGLE_MAP_API_CACHE_KEY';
    const GOOGLE_MAP_API_WEB_CACHE_KEY                              = 'GOOGLE_MAP_API_WEB_CACHE_KEY';
    const WAYBILL_COMPANIES_CACHE_KEY                               = 'WAYBILL_COMPANIES_CACHE_KEY';
    const WAYBILL_CHECK_POINTS_CACHE_KEY                            = 'WAYBILL_CHECK_POINTS_CACHE_KEY';
    const WAYBILL_CHECKERS_CACHE_KEY                                = 'WAYBILL_CHECKERS_CACHE_KEY';
    const WAYBILL_VEHICLES_CACHE_KEY                                = 'WAYBILL_VEHICLES_CACHE_KEY';
    const RENTAL_MERCHANT_EDUCATION_VIDEO_URL_CACHE_KEY             = 'RENTAL_MERCHANT_EDUCATION_VIDEO_URL_CACHE_KEY';
    const RENTAL_USER_EDUCATION_VIDEO_URL_CACHE_KEY                 = 'RENTAL_USER_EDUCATION_VIDEO_URL_CACHE_KEY';
    const DIVISION_LAT_LNG_DATA_SETS_CACHE_KEY                      = 'DIVISION_LAT_LNG_DATA_SETS_CACHE_KEY';
    const RENTAL_MIN_BID_AMOUNT_CACHE_KEY                           = 'RENTAL_MIN_BID_AMOUNT_CACHE_KEY';
    const RENTAL_VEHICLE_TYPE_WISE_REG_BONUS_CACHE_KEY              = 'RENTAL_VEHICLE_TYPE_WISE_REG_BONUS_CACHE_KEY';
    const DIGITAL_TICKETING_FROM_TO_DATA_SETS_CACHE_KEY             = 'DIGITAL_TICKETING_FROM_TO_DATA_SETS_CACHE_KEY';
    const DIGITAL_TICKETING_COUNTERS_FOR_WEB_CACHE_KEY              = 'DIGITAL_TICKETING_COUNTERS_FOR_WEB_CACHE_KEY';
    const DIGITAL_TICKETING_ON_GOING_REQUEST_CACHE_KEY              = 'DIGITAL_TICKETING_ON_GOING_REQUEST_CACHE_KEY';
    const DIGITAL_TICKETING_REQUEST_AVAILABLE_TIME_CACHE_KEY        = 'DIGITAL_TICKETING_REQUEST_AVAILABLE_TIME_CACHE_KEY';
    const DIGITAL_TICKETING_PAYMENT_AVAILABLE_TIME_CACHE_KEY        = 'DIGITAL_TICKETING_PAYMENT_AVAILABLE_TIME_CACHE_KEY';
    const DIGITAL_TICKETING_PAYMENT_GATEWAY_TIMEOUT_CACHE_KEY       = 'DIGITAL_TICKETING_PAYMENT_GATEWAY_TIMEOUT_CACHE_KEY';
    const DIGITAL_TICKETING_USER_MAX_SERVICE_SELECTION_CACHE_KEY    = 'DIGITAL_TICKETING_USER_MAX_SERVICE_SELECTION_CACHE_KEY';
    const DIGITAL_TICKETING_NAGAD_PROMOTIONAL_TEXT_CACHE_KEY        = 'DIGITAL_TICKETING_NAGAD_PROMOTIONAL_TEXT_CACHE_KEY';
    const DIGITAL_TICKETING_SHUTDOWN_REQUEST_CACHE_KEY              = 'DIGITAL_TICKETING_SHUTDOWN_REQUEST_CACHE_KEY';

    public function __construct()
    {
    }

    public function getGoogleMapAPI(){
        return Cache::remember(CacheHelper::GOOGLE_MAP_API_CACHE_KEY, CacheHelper::CACHE_TIME_240H, function () {
            $data = DB::table('settings')
                ->where('key', 'google_map_api')
                ->first();
            return json_decode($data == null ? "{}" : $data->value, true);
        });
    }

    public function getGoogleMapAPIWeb(){
        return Cache::remember(CacheHelper::GOOGLE_MAP_API_WEB_CACHE_KEY, CacheHelper::CACHE_TIME_240H, function () {
            $data = DB::table('settings')
                ->where('key', 'google_map_api_web')
                ->first();
            return json_decode($data == null ? "{}" : $data->value, true);
        });
    }

    public function getActiveTicketingCompanies(){
        return Cache::remember(CacheHelper::TRACKING_TICKETING_COMPANY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('j_vehicle_companies')
                ->where('status', 1)
                ->where('type', 'BUS')
                ->select('id', 'name', 'mobile', 'commission_percentage')
                ->get();
        });
    }

    public function getActiveSupervisorTicketingCompanies(){
        return Cache::remember(CacheHelper::SUPERVISOR_TICKETING_COMPANY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('supervisor_companies')
                ->where('status', 1)
                ->select('id', 'name', 'mobile', 'fare', 'commission_percentage')
                ->get();
        });
    }

    public function getSupervisorCountermen(){
        return Cache::remember(CacheHelper::SUPERVISOR_COUNTERMEN_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('supervisor_countermen')
                ->select('id', 'name', 'phone', 'supervisor_company_id', 'supervisor_bus_number')
                ->get();
        });
    }

    public function getActiveVehicles(){
        return Cache::remember(CacheHelper::TRACKING_TICKETING_VEHICLE_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('j_vehicles')
                ->where('status', 1)
                ->select('id', 'name', 'registration_number', 'tracking_availability', 'booking_availability', 'type', 'vehicle_company_id', 'vehicle_owner_id', 'representation_code', 'track_road_id', 'ride_direction_sequential', 'seat_capacity')
                ->orderBy('registration_number', 'asc')
                ->get();
        });
    }

    public function getActiveRoads(){
        return Cache::remember(CacheHelper::TRACKING_TICKETING_ROAD_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('j_roads')
                ->where('status', 1)
                ->select('id', 'name', 'sequence')
                ->get();
        });
    }

    public function getTicketingFareModalities(){
        return Cache::remember(CacheHelper::TICKETING_FARE_MODALITY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('j_ticket_fares')
                ->orderBy('id', 'asc')
                ->select('id', 'from_stoppage_id', 'to_stoppage_id', 'company_id', 'fare')
                ->get();
        });
    }

    public function getActiveTollPlazaCompanies(){
        return Cache::remember(CacheHelper::TOLL_PLAZA_COMPANY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('toll_plaza_companies')
                ->where('status', 1)
                ->select('id', 'name', 'mobile', 'toll_amount', 'commission_percentage')
                ->get();
        });
    }

    public function getTollPlazaCollectors(){
        return Cache::remember(CacheHelper::TOLL_PLAZA_COLLECTORS_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('toll_plaza_collectors')
                ->select('id', 'name', 'phone', 'tollplaza_company_id', 'identifier', 'entry_enable', 'overstay_enable', 'monthly_enable', 'exit_enable')
                ->get();
        });
    }

    public function getActiveFuelCompanies(){
        return Cache::remember(CacheHelper::FUEL_SERVICE_COMPANY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('fuel_companies')
                ->where('status', 1)
                ->select('id', 'name', 'phone', 'commission_percentage')
                ->get();
        });
    }

    public function getFuelServiceSalesman(){
        return Cache::remember(CacheHelper::FUEL_SERVICE_SALESMAN_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('fuel_salesmen')
                ->select('id', 'name', 'phone', 'company_id', 'machine_id')
                ->get();
        });
    }

    public function getFuelServiceMachines(){
        return Cache::remember(CacheHelper::FUEL_SERVICE_MACHINE_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('fuel_machines')
                ->select('id', 'name', 'tank_id')
                ->get();
        });
    }

    public function getFuelServiceTanks(){
        return Cache::remember(CacheHelper::FUEL_SERVICE_TANK_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('fuel_tanks')
                ->select('id', 'name', 'company_id', 'unit', 'unit_price', 'disburse_per_unit', 'capacity', 'fuel_type', 'selling_units')
                ->get();
        });
    }

    public function getActiveGpCompanies(){
        return Cache::remember(CacheHelper::GP_SERVICE_COMPANY_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('gp_companies')
                ->where('status', 1)
                ->select('id', 'name', 'mobile', 'amount', 'commission_percentage')
                ->get();
        });
    }

    public function getActiveGpVehicles(){
        return Cache::remember(CacheHelper::GP_SERVICE_VEHICLE_CACHE_KEY, CacheHelper::CACHE_TIME_6H, function () {
            return DB::table('gp_vehicles')
                ->where('status', 1)
                ->select('id', 'name', 'registration_number', 'company_id')
                ->get();
        });
    }

    public function getRentalAvailableServiceTypes(){
        return Cache::remember(CacheHelper::RENTAL_AVAILABLE_SERVICE_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            return DB::table('rental_service_types')
                ->where('status', 1)
                ->orderBy('sequence')
                ->get();
        });
    }

    public function getRentalAllServiceTypes(){
        return Cache::remember(CacheHelper::RENTAL_ALL_SERVICE_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            return DB::table('rental_service_types')
                ->select('id', 'name', 'description', 'status', 'sequence', 'logo')
                ->orderBy('sequence')
                ->get();
        });
    }

    public function getRentalShutdownData(){
        return Cache::remember(CacheHelper::RENTAL_SHUTDOWN_REQUEST_CACHE_KEY, CacheHelper::CACHE_TIME_24H * 30, function () {
            $data = DB::table('settings')
                ->where('key', 'rental_shutdown_request_data')
                ->first();

            return $data != null ? $data->value : '{}';
        });
    }

    public function getRentalOwnerRegistrationBonusCredit(){
        return Cache::remember(CacheHelper::RENTAL_OWNER_REGISTRATION_BONUS_CREDIT_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            $data = DB::table('settings')
                ->where('key', 'rental_owner_registration_bonus_credit')
                ->first();

            return $data != null ? $data->value : 0;
        });
    }

    public function getRentalPendingBookingRequests($sync = false){
        try{
            if($sync){
                Cache::forget(CacheHelper::RENTAL_PENDING_BOOKING_REQUESTS_CACHE_KEY);
            }
            return Cache::remember(CacheHelper::RENTAL_PENDING_BOOKING_REQUESTS_CACHE_KEY, CacheHelper::CACHE_TIME, function () {
                return RentalBooking::where('status', 'BIDDING')->orderBy('id', 'desc')->with('service_type')->get();
            });
        }catch (\Exception $ex){
            if($sync == false) $this->getRentalPendingBookingRequests(true);
        }
    }

    public function getRentalOwnerBiddings($owner_id, $sync = false){
        try{
            if($sync){
                Cache::forget(CacheHelper::RENTAL_OWNER_BIDDINGS_CACHE_KEY.'_'.$owner_id);
            }
            return Cache::remember(CacheHelper::RENTAL_OWNER_BIDDINGS_CACHE_KEY.'_'.$owner_id, CacheHelper::CACHE_TIME, function () use ($owner_id) {
                return DB::table('rental_booking_biddings')->select('booking_id')->where('active', 1)->where('owner_id', $owner_id)->pluck('booking_id')->toArray();
            });
        }catch (\Exception $ex){
            if($sync == false) $this->getRentalOwnerBiddings($owner_id, true);
        }
    }

    public function getRentalWaitingBookingRequests($sync = false){
        try {
            if($sync){
                Cache::forget(CacheHelper::RENTAL_WAITING_BOOKING_REQUESTS_CACHE_KEY);
            }
            return Cache::remember(CacheHelper::RENTAL_WAITING_BOOKING_REQUESTS_CACHE_KEY, CacheHelper::CACHE_TIME, function () {
                return RentalBooking::where('status', 'WAITING')->with('service_type')->get();
            });
        }catch (\Exception $ex){
            if($sync == false) $this->getRentalWaitingBookingRequests(true);
        }
    }

    public function getRentalBiddings($booking_id, $sync = false){
        try{
            if($sync){
                Cache::forget(CacheHelper::RENTAL_BIDDINGS_CACHE_KEY.'_'.$booking_id);
            }
            return Cache::remember(CacheHelper::RENTAL_BIDDINGS_CACHE_KEY.'_'.$booking_id, CacheHelper::CACHE_TIME, function () use ($booking_id) {
                return RentalBookingBidding::where('booking_id', $booking_id)->where('active', 1)->with('owner', 'vehicle')->orderBy('bidding_price', 'asc')->get();
            });
        }catch (\Exception $ex){
            if($sync == false) $this->getRentalBiddings($booking_id, true);
        }
    }

    public function getRentalPromocode($promocode){
        return Cache::remember(CacheHelper::RENTAL_PROMOCODE_CACHE_KEY.'_'.$promocode, CacheHelper::CACHE_TIME_24H, function () use ($promocode) {
            return Promocode::where('promocode', $promocode)->first();
        });
    }

    public function getRentalMaxBiddingInSingleRequest(){
        return Cache::remember(CacheHelper::RENTAL_MAX_BIDDINGS_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            $data = DB::table('settings')
                ->where('key', 'rental_max_bidding_in_single_request')
                ->first();

            return $data != null ? $data->value : 100;
        });
    }

    public function getRentalOwnerVehicles($owner_id, $sync = false){
        if($sync){
            Cache::forget(CacheHelper::RENTAL_OWNER_VEHICLE_CACHE_KEY.'_'.$owner_id);
        }
        return Cache::remember(CacheHelper::RENTAL_OWNER_VEHICLE_CACHE_KEY.'_'.$owner_id, CacheHelper::CACHE_TIME_24H, function () use($owner_id){
            return RentalVehicle::where('owner_id', $owner_id)
                ->with('service_type')
                ->get();
        });
    }

    public function getRentalOwnerDrivers($owner_id, $sync = false){
        if($sync){
            Cache::forget(CacheHelper::RENTAL_OWNER_DRIVER_CACHE_KEY.'_'.$owner_id);
        }
        return Cache::remember(CacheHelper::RENTAL_OWNER_DRIVER_CACHE_KEY.'_'.$owner_id, CacheHelper::CACHE_TIME_24H, function () use($owner_id){
            return DB::table('rental_drivers')
                ->where('owner_id', $owner_id)
                ->get();
        });
    }

    public function getWaybillCompanies(){
        return Cache::remember(CacheHelper::WAYBILL_COMPANIES_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function (){
            return DB::table('waybill_companies')
                ->where('status', 1)
                ->select('id','name','mobile','email','address','contact','logo')
                ->get();
        });
    }

    public function getWaybillCheckPoints(){
        return Cache::remember(CacheHelper::WAYBILL_CHECK_POINTS_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function (){
            return DB::table('waybill_check_points')
                ->where('status', 1)
                ->get();
        });
    }

    public function getWaybillCheckPointsCompanyWise($company_id){
        return $this->getWaybillCheckPoints()->where('company_id', $company_id)->values()->all();
    }

    public function getWaybillCheckersCompanyWise($company_id){
        $list = Cache::remember(CacheHelper::WAYBILL_CHECKERS_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            return DB::table('waybill_checkers')
                ->where('status', 1)
                ->select('id','name','mobile','direction','status','company_id')
                ->get();
        });
        return $list->where('company_id', $company_id)->values()->all();
    }

    public function getWaybillVehicles(){
        return Cache::remember(CacheHelper::WAYBILL_VEHICLES_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            return DB::table('waybill_vehicles')
                ->where('status', 1)
                ->select('id','registration_number','status','company_id')
                ->get();
        });
    }

    public function getRentalMerchantLearningVideo(){
        return Cache::remember(CacheHelper::RENTAL_MERCHANT_EDUCATION_VIDEO_URL_CACHE_KEY, CacheHelper::CACHE_TIME_240H, function () {
            $data = DB::table('settings')
                ->where('key', 'rental_merchant_education_video_url')
                ->first();
            return $data != null ? $data->value : '';
        });
    }

    public function getRentalUserLearningVideo(){
        return Cache::remember(CacheHelper::RENTAL_USER_EDUCATION_VIDEO_URL_CACHE_KEY, CacheHelper::CACHE_TIME_240H, function () {
            $data = DB::table('settings')
                ->where('key', 'rental_user_education_video_url')
                ->first();
            return $data != null ? $data->value : '';
        });
    }

    public function getDivisionLatLngDataSets(){
        return Cache::remember(CacheHelper::DIVISION_LAT_LNG_DATA_SETS_CACHE_KEY, CacheHelper::CACHE_TIME_240H, function () {
            $data = DB::table('settings')
                ->where('key', 'division_lat_lng_data_sets')
                ->first();
            return $data != null ? json_decode($data->value, true) : [];
        });
    }

    public function getRentalMinBidAmount(){
        return Cache::remember(CacheHelper::RENTAL_MIN_BID_AMOUNT_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function (){
            $data = DB::table('settings')
                ->where('key', 'rental_min_bidding_amount_in_single_request')
                ->first();
            return $data != null ? round($data->value) : 500;
        });
    }

    public function getRentalVehicleTypeWiseRegBonus(){
        return Cache::remember(CacheHelper::RENTAL_VEHICLE_TYPE_WISE_REG_BONUS_CACHE_KEY, CacheHelper::CACHE_TIME_24H, function () {
            $data = DB::table('settings')->where('key', 'rental_vehicle_type_wise_reg_bonus')->where('status', true)->first();

            return json_decode($data == null ? "{}" : $data->value, true);
        });
    }

    public function getRentalFavouriteRoutes(){
        return Cache::rememberForever(CacheHelper::RENTAL_FAVOURITE_ROUTE_CACHE_KEY, function () {
            return RentalFavouriteRoute::where('status', 1)->with('service_type')->get();
        });
    }

    public function getRentalBookForEvents(){
        return Cache::rememberForever(CacheHelper::RENTAL_BOOK_FOR_EVENT_CACHE_KEY, function () {
            return RentalServiceType::WhereHas('events', function ($query) {
                    return $query->where('status', 1);
                })->with(['events' => function ($query) {
                        $query->where('status',1);
                    }
                ])
                ->select('id','name','description','sequence','logo')
                ->orderBy('sequence')
                ->get();
        });
    }

    // digital ticketing

    public function getDigitalTicketingFromToDataSets(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_FROM_TO_DATA_SETS_CACHE_KEY, function (){
            $data = DB::table('dt_intercity_fares')
                ->leftjoin('dt_intercity_services', 'dt_intercity_services.id', '=', 'dt_intercity_fares.service_id')
                ->where('dt_intercity_services.status', 1)
                ->select('dt_intercity_fares.from', 'dt_intercity_fares.to')
                ->get();

            $all_from_counter = collect($data->pluck('from')->toArray())->unique()->values();

            $processed_data = [];
            foreach ($all_from_counter as $counter){
                $temp['from_counter'] = $counter;
                $temp['to_counter'] = collect($data->where('from', $counter)->pluck('to')->toArray())->unique()->values();
                $processed_data[] = $temp;
            }
            return $processed_data;
        });
    }

    public function getDigitalTicketingCountersForWeb(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_COUNTERS_FOR_WEB_CACHE_KEY, function (){
            $data = DB::table('dt_intercity_fares')
                ->leftjoin('dt_intercity_services', 'dt_intercity_services.id', '=', 'dt_intercity_fares.service_id')
                ->where('dt_intercity_services.status', 1)
                ->select('dt_intercity_fares.from', 'dt_intercity_fares.to')
                ->get();
            $stoppages = array_unique(array_merge($data->pluck('from')->toArray(), $data->pluck('to')->toArray()));
            sort($stoppages);
            return $stoppages;
        });
    }

    public function getDigitalTicketingActiveRequests($sync = false){
        try{
            if($sync){
                Cache::forget(CacheHelper::DIGITAL_TICKETING_ON_GOING_REQUEST_CACHE_KEY);
            }
            return Cache::remember(CacheHelper::DIGITAL_TICKETING_ON_GOING_REQUEST_CACHE_KEY, CacheHelper::CACHE_TIME_10_MIN, function () {
                return DtIntercityBookingRequestFilter::where('status', 'ACTIVE')
                    ->with('booking_service_with_details','booking')
                    ->orderBy('id', 'desc')->get();
            });
        }catch (\Exception $ex){
            if($sync == false) $this->getDigitalTicketingActiveRequests(true);
        }
    }

    public function forgetDigitalTicketingActiveRequests(){
        Cache::forget(CacheHelper::DIGITAL_TICKETING_ON_GOING_REQUEST_CACHE_KEY);
    }

    public function getDigitalTicketingRequestAvailableTime(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_REQUEST_AVAILABLE_TIME_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_request_available_time')
                ->first();

            return $data != null ? round($data->value) : 300;
        });
    }

    public function getDigitalTicketingPaymentAvailableTime(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_PAYMENT_AVAILABLE_TIME_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_payment_available_time')
                ->first();

            return $data != null ? round($data->value) : 600;
        });
    }

    public function getDigitalTicketingPaymentGatewayTimeout(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_PAYMENT_GATEWAY_TIMEOUT_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_payment_gateway_timeout')
                ->first();
            return $data != null ? round($data->value) : 300;
        });
    }

    public function getDigitalTicketingUserMaxServiceSelection(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_USER_MAX_SERVICE_SELECTION_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_user_max_service_selection')
                ->first();
            return $data != null ? round($data->value) : 3;
        });
    }

    public function getDigitalTicketingNagadPromotionalText(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_NAGAD_PROMOTIONAL_TEXT_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_nagad_promotional_text')
                ->first();
            return $data != null ? $data->value : '' ;
        });
    }

    public function getDigitalTicketingShutdownData(){
        return Cache::rememberForever(CacheHelper::DIGITAL_TICKETING_SHUTDOWN_REQUEST_CACHE_KEY, function () {
            $data = DB::table('settings')
                ->where('key', 'digital_ticketing_shutdown_data')
                ->first();

            return $data != null ? $data->value : '{}';
        });
    }

}
