<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAdminAuth
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
            $admin = json_decode(JWTAuth::getPayload(JWTAuth::getToken()));
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 402);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 403);
            }
        }
        $request->admin_id = $admin->admin->id;
        return $next($request);
    }
}
