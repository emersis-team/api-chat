<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\UserFromJWTController;
use App\User;

class UserIsAdminMiddleware
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
        //Valida que el usuario logueado sea ADMIN
        $user_id = UserFromJWTController::getUserIdFromJWT();
        $admin = User::where('id',$user_id)->first('admin');
        $is_admin = $admin['admin'];

        if($is_admin){
            return $next($request);
        }else{
            return response()->json([
                'error' => "El Usuario debe se ADMINISTRADOR.",
            ], 422);
        }
    }
}
