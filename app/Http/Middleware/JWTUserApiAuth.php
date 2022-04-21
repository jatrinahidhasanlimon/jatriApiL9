<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTUserApiAuth
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

            $userObj = User::find($user->sub);
            if($userObj == null || $userObj->status == 0){
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Your account is blocked.'], 400);
            }
            /*if($userObj->api_token != null && $userObj->api_token != $request->header('Authorization')){
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'You have logged in another device before. Please login now again.'], 402);
            }*/

            $request->user_id = $user->sub;
            $request->user = $userObj;
            return $next($request);
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $user = User::where('api_token', $request->header('Authorization'))->first();
                if($user){
                    return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Token Expired. Trying To Regenerate.'], 401);
                }else{
                    return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Invalid Token. Login Now'], 402);
                }
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Invalid Token. Login Now'], 402);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'Invalid Token. Login Now'], 402);
            }
        }
    }
}
