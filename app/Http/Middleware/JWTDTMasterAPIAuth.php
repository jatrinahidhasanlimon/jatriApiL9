<?php

namespace App\Http\Middleware;

use App\Model\DtIntercityMaster;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTDTMasterAPIAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            JWTAuth::setToken($request->header('Authorization'));
            $user = json_decode(JWTAuth::getPayload(JWTAuth::getToken()));

            //if($user->env == config('app.env') && $user->type == 'dt_agent') {
                $master = DtIntercityMaster::find($user->sub);
                if ($master == null || $master->api_token != $request->header('Authorization')) {
                    return response()->json(['status' => 'error', 'data' => '', 'message' => 'You have logged in another device before. Please login now again.'], 402);
                }
                $request->dt_master = $master;
                return $next($request);
            //}
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 402);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 402);
            }
        }
        return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 402);
    }
}
