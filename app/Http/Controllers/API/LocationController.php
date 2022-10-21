<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Http\Controllers\UserFromJWTController;
use App\Models\Location;
use App\Models\User;

class LocationController extends Controller
{
    public function getLocations()
    {
        //Si el user_name que se envía por JWT existe -> devuelve el user_id, si no existe devuelve error 404
        $user_name = UserFromJWTController::getUserNameFromJWT();
        $user_id = UserFromJWTController::getUserIdFromJWT();

        try {
            //Chequea que exista el usuario con ese user_name o user_id (según corresponda)

            if($user_name){
                echo "USER NAME: " . $user_name . "\n";
                $user = User::where('user_name',$user_name)->first();

            }else{
                echo "USER ID: " . $user_id;
                $user = User::find($user_id) . "\n";
            }

            // echo "USER NAME: " . $user->name . "\n";
            // echo "USER ID: " . $user->id . "\n";

            if ($user == null) {
                return response()->json([
                    'error' => "No exste el usuario.",
                ], 404);
            }

            return response()->json([
                'user_id' => $user->id,
            ]);
        }

        catch (QueryException $e) {
            throw new \Error('Hay un Error SQL');
        }

        catch (\Throwable $e) {

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function getLocation(Location $location)
    {

    }

    public function createLocation(Request $request)
    {

    }

    public function updateLocation(Request $request)
    {

    }

}
