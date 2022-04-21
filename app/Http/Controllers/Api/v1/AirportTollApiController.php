<?php

namespace App\Http\Controllers\Api\v1;
use App\AirportTollman;
use App\Helper\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Common\ThirdPartyServiceManager;
use App\Http\Controllers\Utility\Common\Utils;
use Illuminate\Http\Request;
use JWTAuth;
use Hash;
use Config;
use DB;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class AirportTollApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt_auth_airport_tollman_api', ['except' => ['login', 'refreshToken']]);
    }

    public function login(Request $request){
        $tollman  = AirportTollman::where('phone', '=', get_phone_by_adding_country_code($request->phone))->first();

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
                    if (!$token = JWTAuth::customClaims(['airport_tollman' => $tollman])->fromUser($tollman)) {
                        return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
                    }else{
                        $tollman->api_token = $token;
                        $tollman->save();
                        (new Utils)->saveLogEvent(null, ['new_token' => $token], ['airport tollman new login'], ['user_type' => 'airport_tollman', 'user_id' => $tollman->id]);
                    }
                } catch (JWTException $e) {
                    return response()->json(['status'=>'error', 'token'=>'', 'message'=>$e->getMessage()], 422);
                }
            }
            else {
                return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
            }
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'message' => 'Login Successful!',
            'tollman' => $tollman
        ]);
    }

    public function refreshToken(Request $request){
        try{
            $request->validate([
                'last_token' => 'required'
            ]);
            $tollman  = AirportTollman::where('api_token', '=', $request->last_token)->first();
            if($tollman){
                if ($tollman->status == 0) {
                    return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Your account is not yet activated!'], 422);
                }else if($tollman->status == 1){
                    try {
                        if (!$token = JWTAuth::customClaims(['airport_tollman' => $tollman])->fromUser($tollman)) {
                            return response()->json(['status'=>'error', 'token'=>'', 'message'=>'Invalid Credentials!'], 422);
                        }else{
                            $tollman->api_token = $token;
                            $tollman->save();
                            (new Utils)->saveLogEvent(null, ['new_token' => $token], ['airport tollman token refresh'], ['user_type' => 'airport_tollman', 'user_id' => $tollman->id]);
                            return response()->json([
                                'status' => 'success',
                                'token' => $token,
                                'message' => 'Token Refreshed!',
                                'tollman' => $tollman
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
            $tollman = AirportTollman::findOrFail($request->airport_tollman->id);
            $tollman->api_token = null;
            $tollman->save();
            (new Utils)->saveLogEvent(null, null, ['airport tollman log out'], ['user_type' => 'airport_tollman', 'user_id' => $tollman->id]);
            $res['message'] = 'Logged out successfully';
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
            return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
        }
    }

    public function getProfile(Request $request){
        try{
            $res = ControllerHelper::getSuccessResponseFormat();
            $res['data'] = $request->airport_tollman;
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
            ]);

            $toll_collection = $request->toll_collection;
            $submitted_date = date('Y-m-d H:i:s');

            $collection_to_be_insert = [];
            foreach ($toll_collection as $toll){
                $collection_to_be_insert[] = [
                    'serial' => $toll['serial'],
                    'vehicle_number' => $toll['vehicle_number'],
                    'amount' => $toll['amount'],
                    'unit_amount' => $toll['unit_amount'],
                    'hour' => $toll['hour'],
                    'type' => $toll['type'],
                    'status' => 1,
                    'tollman_id' => $request->airport_tollman->id,
                    'date_only' => substr($toll['created_at'], 0, 10),
                    'created_at' => $toll['created_at'],
                    'updated_at' => $submitted_date
                ];
            }

            DB::table('airport_toll_collections')->insert($collection_to_be_insert);

            $res = ControllerHelper::getSuccessResponseFormat();
            $res['message'] = 'Data Synced Successfully!';

            //check repeated ticket if found then remove
            $this->validateSyncedTollCollectionData($request->airport_tollman->id);

            return response()->json($res);
        }catch (\Exception $ex){
            Log::info('Airport Tollman API: Synced Toll Collection Data : TollmanID: '. $request->airport_tollman->id);
            Log::info('Airport Tollman API: Synced Toll Collection Data : Collection', $request->toll_collection);

            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
            return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
        }
    }

    private function validateSyncedTollCollectionData($tollman_id){
        try{
            /*$hasRepeatedCollection = DB::table('airport_toll_collections')->whereIn('id', function ($query) use ($tollman_id) {
                $query->select('id')->from('airport_toll_collections')
                    ->where('tollman_id', $tollman_id)
                    ->where('date_only', date('Y-m-d'))
                    ->groupBy('created_at')
                    ->groupBy('serial')
                    ->havingRaw('count(*) > 1');
            })->pluck('id');*/

            $hasRepeatedCollection = DB::table('airport_toll_collections')
                ->where('tollman_id', $tollman_id)
                ->where('date_only', date('Y-m-d'))
                ->groupBy('created_at')
                ->groupBy('serial')
                ->havingRaw('count(id) > 1')
                ->pluck('id');

            if(count($hasRepeatedCollection) > 0){
                DB::table('airport_toll_collections')->whereIn('id', $hasRepeatedCollection)->delete();
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
            $this->validateSyncedTollCollectionData($request->airport_tollman->id);

            $totals = DB::table('airport_toll_collections')
                ->where('tollman_id', $request->airport_tollman->id)
                ->where('date_only', date('Y-m-d'))
                ->where('status', 1)
                ->get();

            $res = ControllerHelper::getSuccessResponseFormat();
            $res['synced_today_entry_fee_amount'] = $totals->where('type', 'ENTRY_FEE')->sum('amount');
            $res['synced_today_entry_fee_count'] = $totals->where('type', 'ENTRY_FEE')->count();
            $res['synced_today_overstay_fee_amount'] = $totals->where('type', 'OVERSTAY_FEE')->sum('amount');
            $res['synced_today_overstay_fee_count'] = $totals->where('type', 'OVERSTAY_FEE')->count();
            return response()->json($res);
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json(ControllerHelper::getErrorResponseFormat(), 500);
    }

    public function printReport(Request $request){
        try{
            $err = ControllerHelper::getErrorResponseFormat();
            $cache_key = 'apt_print_count_'.date('Y_m_d').'_'.$request->airport_tollman->id;
            $today_printed = cache()->remember($cache_key, 86400, function (){
                return 0;
            });
            if($today_printed < 2){
                cache()->increment($cache_key);
                if($request->has('report_date') && $request->report_date != ''){
                    $report_date = $request->report_date;
                }else{
                    $report_date = date('Y-m-d');
                }

                $totals = DB::table('airport_toll_collections')
                    ->where('tollman_id', $request->airport_tollman->id)
                    ->where('date_only', $report_date)
                    ->where('status', 1)
                    ->get();

                $res = ControllerHelper::getSuccessResponseFormat();
                $res['synced_today_entry_fee_amount'] = $totals->where('type', 'ENTRY_FEE')->sum('amount');
                $res['synced_today_entry_fee_count'] = $totals->where('type', 'ENTRY_FEE')->count();
                $res['synced_today_overstay_fee_amount'] = $totals->where('type', 'OVERSTAY_FEE')->sum('amount');
                $res['synced_today_overstay_fee_count'] = $totals->where('type', 'OVERSTAY_FEE')->count();

                $amount_wise_distributions = $totals->groupBy('amount')->all();
                $distribution_data = [];
                foreach ($amount_wise_distributions as $key => $data){
                    $d['amount'] = $key;
                    $d['quantity'] = $data->count();
                    $d['total_amount'] = $data->sum('amount');
                    $distribution_data[] = $d;
                }
                $res['amount_wise_distributions'] = $distribution_data;
                $res['report_date'] = $report_date;
                $res['is_printing_enable'] = true;
                return response()->json($res);
            }else{
                $err['is_printing_enable'] = false;
                $err['message'] = 'Report Printing Limit Over';
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
        return response()->json($err, 500);
    }
}
