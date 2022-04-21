<?php

namespace App\Http\Middleware;

use App\Model\RentalOwner;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTRentalOwnerAPIAuth
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
            $owner = json_decode(JWTAuth::getPayload(JWTAuth::getToken()));

            //if($owner->env == config('app.env') && $owner->type == 'rental_owner'){
                $ownerObj = RentalOwner::find($owner->sub);
                if($ownerObj == null || $ownerObj->api_token != $request->header('Authorization')){
                    return response()->json(['status'=>'error', 'data'=>'', 'message'=>'You have logged in another device before. Please login now again.'], 402);
                }
                $request->rental_owner = $ownerObj;
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
        //return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 402);
    }
}
