<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTUserAuth
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
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 401);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 401);
            }
        }
        /*
        $tokenIsValid = User::where('id', $user->sub)->where('api_token', $request->header('Authorization'))->first();
        if($tokenIsValid == null){
            return response()->json(['status'=>'error', 'data'=>'', 'message'=>'previous_token'], 400);
        }
        */
        $userObj = User::where('id', $user->sub)->first();
        if($userObj->status == 0){
            return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Your account is blocked.'], 400);
        }
        if($userObj->api_token != null && $userObj->api_token != $request->header('Authorization')){
            return response()->json(['status'=>'error', 'data'=>'', 'message'=>'previous_token'], 401);
        }

        $request->user_id = $user->sub;
        return $next($request);
    }
}
