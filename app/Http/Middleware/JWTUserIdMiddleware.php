<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\UserFromJWTController;

class JWTUserIdMiddleware
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
        $user_id = UserFromJWTController::getUserIdFromJWT();

        if($user_id){
            return $next($request);
        }else{
            return response()->json([
                'error' => "El token NO contiene el user_id.",
            ], 422);
        }
    }
}
