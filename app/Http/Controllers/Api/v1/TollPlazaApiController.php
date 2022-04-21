<?php

namespace App\Http\Controllers\Api\v1;
use App\Helper\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\CacheHelper;
use App\Http\Controllers\Utility\Common\ThirdPartyServiceManager;
use App\Http\Controllers\Utility\Common\Utils;
use App\Model\TollPlazaCollection;
use App\Model\TollPlazaReportPrint;
use App\TollPlazaCollector;
use Illuminate\Http\Request;
use JWTAuth;
use Hash;
use Config;
use DB;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class TollPlazaApiController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt_auth_toll_collector_api', ['except' => ['login', 'refreshToken']]);
    }

    public function login(Request $request){
        $tollman  = TollPlazaCollector::where('phone', '=', get_phone_by_adding_country_code($request->phone))->first();

        if (!$tollman) {
            return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
        }

        if ($tollman->status == 0) {
            return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Your account is not yet activated!'], 422);
        }else if ($tollman->api_token != null) {
            return response()->json(['status'=>'error', 'token'=>'', 'message'=>'You already logged in before. Please logout previous device or contact your line manager!'], 422);
        }else if($tollman->status == 1){
            if (Hash::check($request->password, $tollman->password)) {
                try {
                    if (!$token = JWTAuth::customClaims(['type' => 'tollplaza_collector', 'env' => config('app.env')])->fromUser($tollman)) {
                        return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
                    }else{
                        $tollman->api_token = $token;
                        $tollman->save();
                        (new Utils)->saveLogEvent(null, ['new_token' => $token], ['toll plaza collector new login'], ['user_type' => 'tollplaza_collector', 'user_id' => $tollman->id]);
                    }
                } catch (JWTException $e) {
                    return response()->json(['status'=>'error', 'token'=>'', 'message'=>$e->getMessage()], 422);
                }
            } else {
                return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
            }
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'message' => 'Login Successful!',
            'tollplaza_collector' => $tollman
        ]);
    }

    public function refreshToken(Request $request){
        try{
            $request->validate([
                'last_token' => 'required'
            ]);
            $tollman  = TollPlazaCollector::where('api_token', '=', $request->last_token)->first();
            if($tollman){
                if ($tollman->status == 0) {
                    return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Your account is not yet activated!'], 422);
                }else if($tollman->status == 1){
                    try {
                        if (!$token = JWTAuth::customClaims(['type' => 'tollplaza_collector', 'env' => config('app.env')])->fromUser($tollman)) {
                            return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
                        }else{
                            $tollman->api_token = $token;
                            $tollman->save();
                            (new Utils)->saveLogEvent(null, ['new_token' => $token], ['toll plaza collector token refresh'], ['user_type' => 'tollplaza_collector', 'user_id' => $tollman->id]);
                            return response()->json([
                                'status' => 'success',
                                'token' => $token,
                                'message' => 'Token Refreshed!',
                                'tollplaza_collector' => $tollman
                            ]);
                        }
                    } catch (JWTException $e) {
                        return response()->json(['status'=>'error', 'token'=>'', 'message'=>$e->getMessage()], 422);
                    }
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function logout(Request $request){
        try{
            $res = ControllerHelper::getSuccessResponseFormat();
            $tollman = TollPlazaCollector::findOrFail($request->tollplaza_collector->id);
            $tollman->api_token = null;
            $tollman->save();
            (new Utils)->saveLogEvent(null, null, ['toll plaza collector log out'], ['user_type' => 'tollplaza_collector', 'user_id' => $tollman->id]);
            $res['message'] = 'Logged out successfully';
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function getProfile(Request $request){
        try{
            $res = ControllerHelper::getSuccessResponseFormat();
            $res['data'] = $request->tollplaza_collector;
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function getUpdatedOfflineData(Request $request){
        try{
            $max_serial = DB::table('toll_plaza_collections')
                ->where(function ($query) use ($request) {
                    $query->where('toll_creator_id', $request->tollplaza_collector->id)
                        ->orWhere('toll_collector_id', $request->tollplaza_collector->id);
                })
                ->where('date_only', date('Y-m-d'))
                ->count();

            $res = ControllerHelper::getSuccessResponseFormat();
            $company = (new CacheHelper())->getActiveTollPlazaCompanies()->where('id', $request->tollplaza_collector->tollplaza_company_id)->first();
            $res['toll_modality'] = json_decode($company->toll_amount);
            $res['serial'] = $max_serial;
            $res['date_time'] = date('Y-m-d H:i:s');
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
            return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
        }
    }

    public function syncTollCollectionData(Request $request){
        try{
            $request->validate([
                'toll_collection' => 'required',
                'item_count' => 'numeric'
            ]);

            $toll_collection = $request->toll_collection;
            $submitted_date = date('Y-m-d H:i:s');

            $collection_to_be_insert = [];
            foreach ($toll_collection as $toll){
                $collection_to_be_insert[] = [
                    'serial'            => $request->tollplaza_collector->identifier.$toll['serial'],
                    'vehicle_number'    => $toll['vehicle_number'],
                    'amount'            => $toll['amount'],
                    'unit_amount'       => $toll['unit_amount'],
                    'hour'              => $toll['hour'],
                    'type'              => $toll['type'],
                    'status'            => 1,
                    'contact'           => $toll['contact'],
                    'toll_creator_id'   => $request->tollplaza_collector->id,
                    'toll_collector_id' => $request->tollplaza_collector->id,
                    'date_only'         => substr($toll['created_at'], 0, 10),
                    'created_at'        => $toll['created_at'],
                    'exited_at'         => isset($toll['auto_exit']) == true ? ($toll['auto_exit'] == 1 ? $submitted_date : null) : null,
                    'updated_at'        => $submitted_date
                ];
            }

            if(($request->has('item_count') && $request->item_count == count($collection_to_be_insert)) || !$request->has('item_count')){
                DB::table('toll_plaza_collections')->insert($collection_to_be_insert);

                $this->validateSyncedTollCollectionData($request->tollplaza_collector->id);

                $res = ControllerHelper::getSuccessResponseFormat();
                $res['message'] = 'Data Synced Successfully!';

                //check repeated ticket if found then remove
                $this->validateSyncedTollCollectionData($request->tollplaza_collector->id);

                return response()->json($res);
            }
        }catch (\Exception $ex){
            Log::info('TollPlaza API: Synced Toll Collection Data : TollmanID: '. $request->tollplaza_collector->id);
            Log::info('TollPlaza API: Synced Toll Collection Data : Collection', $request->toll_collection);

            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function validateSyncData(Request $request){
        try{
            $this->validateSyncedTollCollectionData( $request->tollplaza_collector->id);
            $res = ControllerHelper::getSuccessResponseFormat();
            $res['message'] = 'Data validated Successfully!';
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    private function validateSyncedTollCollectionData($tollman_id){
        try{
            /*$hasRepeatedCollection = DB::table('toll_plaza_collections')->select('id', 'serial', 'amount')->whereIn('id', function ($query) use ($tollman_id) {
                $query->select('id')->from('toll_plaza_collections')
                    ->where('toll_creator_id', $tollman_id)
                    ->where('date_only', date('Y-m-d'))
                    ->groupBy('created_at')
                    ->groupBy('serial')
                    ->havingRaw('count(*) > 1');
            })->get();*/

            $hasRepeatedCollection = DB::table('toll_plaza_collections')->select('id', 'serial', 'amount')
                ->where('toll_creator_id', $tollman_id)
                ->where('date_only', date('Y-m-d'))
                ->groupBy('created_at')
                ->groupBy('serial')
                ->havingRaw('count(id) > 1')
                ->get();

            if(count($hasRepeatedCollection) > 0){
                $duplicated_ids = $hasRepeatedCollection->pluck('id')->toArray();
                $duplicated_serials = $hasRepeatedCollection->pluck('serial')->toArray();
                $duplicated_amount = $hasRepeatedCollection->sum('amount');

                DB::table('toll_plaza_collections')->whereIn('id', $duplicated_ids)->delete();

                (new Utils)->saveLogEvent(
                    null,
                    [
                        'ticket_ids'        => json_encode($duplicated_ids),
                        'ticket_serials'    => json_encode($duplicated_serials),
                        'ticket_amount'     => $duplicated_amount
                    ], ['auto remove repeated ticket'],
                    [
                        'user_type' => 'tollplaza_collector', 'user_id' => $tollman_id
                    ]
                );

                $this->validateSyncedTollCollectionData($tollman_id);
            }
            return true;
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return false;
    }

    public function getTodaySynced(Request $request){
        try{
            $totals = DB::table('toll_plaza_collections')
                ->where('toll_collector_id', $request->tollplaza_collector->id)
                ->where('date_only', date('Y-m-d'))
                ->where('status', 1)
                ->get();

            $res = ControllerHelper::getSuccessResponseFormat();
            $res['synced_today_entry_fee_amount'] = $totals->where('type', 'ENTRY_FEE')->sum('amount');
            $res['synced_today_entry_fee_count'] = $totals->where('type', 'ENTRY_FEE')->count();
            $res['synced_today_overstay_fee_amount'] = $totals->where('type', 'OVERSTAY_FEE')->sum('amount');
            $res['synced_today_overstay_fee_count'] = $totals->where('type', 'OVERSTAY_FEE')->count();
            $res['synced_today_monthly_fee_amount'] = $totals->where('type', 'MONTHLY_FEE')->sum('amount');
            $res['synced_today_monthly_fee_count'] = $totals->where('type', 'MONTHLY_FEE')->count();
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function exitVehicle(Request $request){
        try{
            $err = ControllerHelper::getErrorResponseFormat();
            $request->validate([
                'ticket_serial'     => 'required',
                'my_serial'         => 'required',
            ]);
            $cacheHelper = new CacheHelper();
            $creator_ids = $cacheHelper->getTollPlazaCollectors()
                ->where('tollplaza_company_id', $request->tollplaza_collector->tollplaza_company_id)
                ->pluck('id')->toArray();
            $entry_fee = TollPlazaCollection::where('type', 'ENTRY_FEE')
                //->where('date_only', date('Y-m-d'))
                ->whereIn('toll_creator_id', $creator_ids)
                ->where('serial', $request->ticket_serial)
                ->orderBy('id', 'desc')
                ->first();

            if($entry_fee){
                if($entry_fee->exited_at == null || $entry_fee->exited_at == "") {

                    $entry_fee->exited_at = date('Y-m-d H:i:s');
                    $entry_fee->exited_by = $request->tollplaza_collector->id;

                    if($request->has('entry_fee_collected') && $request->entry_fee_collected == 1){

                    }else{
                        $entry_fee->toll_collector_id = $request->tollplaza_collector->id;
                    }
                    $entry_fee->save();

                    $res = ControllerHelper::getSuccessResponseFormat();

                    $total_hours = ceil((strtotime('-10 minutes') - strtotime($entry_fee->created_at)) / 3600);
                    $total_hours = $total_hours < 1 ? 1 : $total_hours;
                    $company = $cacheHelper->getActiveTollPlazaCompanies()->where('id', $request->tollplaza_collector->tollplaza_company_id)->first();
                    $toll_modality = json_decode($company->toll_amount);

                    $overstay_amount = 0;
                    $overstay_hour = 0;
                    $date_time = date('Y-m-d H:i:s');
                    if($total_hours > $entry_fee->hour){
                        foreach ($toll_modality->overstay_fees as $obj){
                            if($obj->_metadata_entry_fee == $entry_fee->unit_amount && $obj->_metadata_entry_hour == $entry_fee->hour){

                                $overstay_hour = $total_hours - $entry_fee->hour;
                                $overstay_amount = round(ceil($overstay_hour / $obj->hour) * $obj->fee);

                                $res['overstay_time_unit'] = $obj->hour;
                                $res['overstay_amount_per_time_unit'] = $obj->fee;

                                DB::table('toll_plaza_collections')->insert([
                                    'serial'            => $request->tollplaza_collector->identifier.$request->my_serial,
                                    'vehicle_number'    => $entry_fee->vehicle_number,
                                    'amount'            => $overstay_amount,
                                    'unit_amount'       => $obj->fee,
                                    'hour'              => $overstay_hour,
                                    'type'              => 'OVERSTAY_FEE',
                                    'status'            => 1,
                                    'contact'           => '',
                                    'toll_creator_id'   => $request->tollplaza_collector->id,
                                    'toll_collector_id' => $request->tollplaza_collector->id,
                                    'entry_id'          => $entry_fee->id,
                                    'date_only'         => substr($date_time, 0, 10),
                                    'exited_at'         => $date_time,
                                    'created_at'        => $date_time,
                                    'updated_at'        => $date_time
                                ]);
                                break;
                            }
                        }
                    }

                    $res['vehicle_no'] = $entry_fee->vehicle_number;
                    $res['entry_amount'] = $entry_fee->amount;
                    $res['overstay_amount'] = $overstay_amount;
                    $res['overstay_hour'] = $overstay_hour;
                    $res['total_hour'] = $total_hours;
                    $res['total_fee_amount'] = $entry_fee->unit_amount + $overstay_amount;
                    $res['entry_time'] = date('Y-m-d H:i:s', strtotime($entry_fee->created_at));
                    $res['exit_time'] = $date_time;
                    return response()->json($res);
                } else{
                    $err['message'] = 'Already exited!';
                }
            } else {
                $err['message'] = 'Sorry! Entry not found or not yet submitted to online';
            }

        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json($err, 500);
    }

    public function printReport(Request $request){
        try{
            $request->validate([
                'date_from'     => 'required',
                'date_to'       => 'required',
            ]);
            $err = ControllerHelper::getErrorResponseFormat();
            $today_printed = DB::table('toll_plaza_report_prints')
                ->where('toll_collector_id', $request->tollplaza_collector->id)
                ->where('date_only', date('Y-m-d'))
                ->count();
            if($today_printed < $request->tollplaza_collector->report_print_limit){
                $printReport = new TollPlazaReportPrint();
                $printReport->toll_collector_id = $request->tollplaza_collector->id;
                $printReport->date_only = date('Y-m-d');
                $printReport->save();

                $totals = DB::table('toll_plaza_collections')
                    ->where('toll_collector_id', $request->tollplaza_collector->id)
                    ->whereBetween('created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->get();

                $res = ControllerHelper::getSuccessResponseFormat();
                $res['is_printing_enable'] = true;
                $res['report'] = $printReport;

                $res['synced_today_entry_fee_amount'] = $totals->where('type', 'ENTRY_FEE')->sum('amount');
                $res['synced_today_entry_fee_count'] = $totals->where('type', 'ENTRY_FEE')->count();
                $res['synced_today_overstay_fee_amount'] = $totals->where('type', 'OVERSTAY_FEE')->sum('amount');
                $res['synced_today_overstay_fee_count'] = $totals->where('type', 'OVERSTAY_FEE')->count();
                $res['synced_today_monthly_fee_amount'] = $totals->where('type', 'MONTHLY_FEE')->sum('amount');
                $res['synced_today_monthly_fee_count'] = $totals->where('type', 'MONTHLY_FEE')->count();

                $amount_wise_distributions = $totals->where('status', 1)->groupBy('amount')->all();
                $distribution_data = [];
                foreach ($amount_wise_distributions as $key => $data){
                    $d['amount'] = $key;
                    $d['quantity'] = $data->count();
                    $d['total_amount'] = $data->sum('amount');
                    $distribution_data[] = $d;
                }
                $res['amount_wise_distributions'] = $distribution_data;
                $res['date_form'] = $request->date_from;
                $res['date_to'] = $request->date_to;
                return response()->json($res);
            }else{
                $res['is_printing_enable'] = false;
                $err['message'] = 'Report Printing Limit Over';
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json($err, 500);
    }

}
