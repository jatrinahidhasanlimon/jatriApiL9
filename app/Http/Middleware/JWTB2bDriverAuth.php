<?php

namespace App\Http\Middleware;

use App\B2bDriver;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTB2bDriverAuth
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
            $b2bDriver = json_decode(JWTAuth::getPayload(JWTAuth::getToken()));
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 400);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 400);
            }
        }
        $tokenIsValid = B2bDriver::where('id', $b2bDriver->b2b_driver->id)->where('api_token', $request->header('Authorization'))->first();
        if($tokenIsValid == null){
            return response()->json(['status'=>'error', 'data'=>'', 'message'=>'previous_token'], 400);
        }
        $request->b2b_driver = $b2bDriver->b2b_driver;
        return $next($request);
    }
}
