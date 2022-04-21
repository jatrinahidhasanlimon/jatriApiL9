<?php

namespace App\Http\Middleware;

use App\MicrobusAdministrator;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTMicrobusAdministratorAuth
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
            $microbusAdministrator = json_decode(JWTAuth::getPayload(JWTAuth::getToken()));
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 400);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 400);
            }
        }
        $tokenIsValid = MicrobusAdministrator::where('id', $microbusAdministrator->microbus_administrator->id)->where('api_token', $request->header('Authorization'))->first();
        if($tokenIsValid == null){
            return response()->json(['status'=>'error', 'data'=>'', 'message'=>'previous_token'], 400);
        }
        $request->microbus_administrator = $microbusAdministrator->microbus_administrator;
        return $next($request);
    }
}
