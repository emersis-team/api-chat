<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\UserFromJWTController;

class JWTUserNameMiddleware
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
        $user_name = UserFromJWTController::getUserNameFromJWT();

        if($user_name){
            return $next($request);
        }else{
            return response()->json([
                'error' => "El token NO contiene el user_name.",
            ], 422);
        }
    }
}
