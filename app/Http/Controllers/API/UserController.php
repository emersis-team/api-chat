<?php

namespace App\Http\Controllers\API;

use App\Events\NewPositionEvent;
use App\Models\UserContact;
use App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\UserPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Http\Controllers\UserFromJWTController;

class UserController extends Controller
{
    public function getUserIdOrCreateJWT()
    {
        //Si el user_name que se envía por JWT existe -> devuelve el user_id, si no existe se lo crea y también se devuelve el user_id
        $user_name = UserFromJWTController::getUserName();
        $user_id = UserFromJWTController::getUserId();
        
        try {
            //Chequea que exista el usuario con ese user_name o user_id (según corresponda)

            if($user_name){
                echo "USER NAME: " . $user_name . "\n";
                $user = User::where('user_name',$user_name)->first();

            }else{
                echo "USER ID: " . $user_id;
                $user = User::find($user_id) . "\n";
            }

            echo "USER NAME: " . $user->name . "\n";
            echo "USER ID: " . $user->id . "\n";
            

            if ($user == null) {
                //Crear el usuario
                $user = User::create([
                    'name' => $user_name,
                    'user_name' => $user_name,
                ]);
    
                if (!$user) {
                    throw new \Error('No se pudo crear el usuario.');
                }
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
}
