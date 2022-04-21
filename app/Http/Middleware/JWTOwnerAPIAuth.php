<?php

namespace App\Http\Middleware;

use App\Model\JVehicleOwner;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTOwnerAPIAuth
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

            if($user->env == config('app.env') && $user->type == 'jatri_b2b_partner'){
                $ownerObj = JVehicleOwner::find($user->sub);
                if($ownerObj == null || $ownerObj->api_token != $request->header('Authorization')){
                    return response()->json(['status'=>'error', 'data'=>'', 'message'=>'previous_token'], 402);
                }
                $request->owner = $ownerObj;
                return $next($request);
            }
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
