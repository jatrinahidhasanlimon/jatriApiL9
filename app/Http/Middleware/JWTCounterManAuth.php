<?php

namespace App\Http\Middleware;

use App\CounterMan;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTCounterManAuth
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
            $counterMan = CounterMan::find($user->sub);

            //if($user->env == config('app.env') && $user->type == 'intra_counterman') {
                if ($counterMan == null || $counterMan->api_token != $request->header('Authorization')) {
                    return response()->json(['status' => 'error', 'data' => '', 'message' => 'previous_token'], 400);
                }
                $request->counter_man = $counterMan;
                return $next($request);
            //}
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_expired'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 400);
            }else{
                return response()->json(['status'=>'error', 'data'=>'', 'message'=>$e->getMessage()], 400);
            }
        }
        return response()->json(['status'=>'error', 'data'=>'', 'message'=>'token_invalid'], 400);
    }
}
