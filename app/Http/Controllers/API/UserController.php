<?php

namespace App\Http\Controllers\API;

use App\Events\NewPositionEvent;
use App\Models\UserContact;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
    public function getUserJWT()
    {
        //Si el user_name que se envía por JWT existe -> devuelve el user_id, si no existe devuelve error 404
        $user_name = UserFromJWTController::getUserName();
        $user_id = UserFromJWTController::getUserId();

        try {
            //Chequea que exista el usuario con ese user_name o user_id (según corresponda)

            if($user_name){
                echo "USER NAME: " . $user_name . "\n";
                $user = User::where('user_name',$user_name)->first();

            }else if($user_id){
                echo "USER ID: " . $user_id;
                $user = User::find($user_id) . "\n";
            }else{
                return response()->json([
                    'error' => "El token NO contiene el user_name ni el user_id\n",
                ], 422); 
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

    public function createUserJWT(Request $request)
    {
        //Si el user_name que se envía por JWT existe -> devuelve el user_id, si no existe se lo crea y también se devuelve el user_id
        $user_name = UserFromJWTController::getUserName();

        try {

            if($user_name){
                echo "USER NAME: " . $user_name . "\n";

                //Chequea si existe el usuario con ese user_name, si existe no lo crea
                $user = User::where('user_name',$user_name)->first();

                if ($user == null) {

                    //Chequea los campos de entrada
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'name' => ['required','string', 'max:255'],
                            'surname' => ['required','string', 'max:255'],
                            'grade' => ['nullable','string'],
                            'dni' => ['required','integer'],
                            'location_id' => ['required','integer', 'exists:locations,id'],
                        ]
                    );

                    if ($validator->fails()) {
                        return response()->json([
                            'errors' => $validator->errors(),
                        ], 422);
                    }

                    $campos = $request;

                    //Crear el usuario
                    $user = User::create([
                        'name' => $campos['name'],
                        'surname' => $campos['surname'],
                        'grade' => $campos['grade'],
                        'dni' => $campos['dni'],
                        'location_id' => $campos['location_id'],
                        'user_name' => $user_name,
                    ]);

                    if (!$user) {
                        throw new \Error('No se pudo crear el usuario.');
                    }
                }else{
                    echo "Ya existe el user con el user_name: " . $user->name . "\n";
                }

            }else{
                return response()->json([
                    'error' => "El token NO contiene el user_name\n",
                ], 422); 
            }

            // echo "USER NAME: " . $user->name . "\n";
            // echo "USER ID: " . $user->id . "\n";

            return response()->json([
                'user_id' => $user->id,
            ]);
        }

        catch (\Throwable $e) {
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUserJWT(Request $request)
    {
        $user_id = UserFromJWTController::getUserId();

        try {
            //Chequea que exista el usuario con ese user_name o user_id (según corresponda)

            if($user_id){
                echo "USER ID: " . $user_id;
                $user = User::find($user_id);
            }else{
                return response()->json([
                    'error' => "El token NO contiene el user_id\n",
                ], 422); 
            }
            
            // echo "USER NAME: " . $user->name . "\n";
            // echo "USER ID: " . $user->id . "\n";


            if ($user != null) {
                //Chequea los campos de entrada
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => ['required','string', 'max:255'],
                        'surname' => ['required','string', 'max:255'],
                        'grade' => ['nullable','string'],
                        'dni' => ['required','integer'],
                        'location_id' => ['required','integer', 'exists:locations,id'],
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $campos = $request;

                //Actualiza el usuario
                User::where('id', $user_id)
                            ->update([
                                'name' => $campos['name'],
                                'surname' => $campos['surname'],
                                'grade' => $campos['grade'],
                                'dni' => $campos['dni'],
                                'location_id' => $campos['location_id'],
                                ]);


            }else{
                return response()->json([
                    'error' => "No exste el usuario.",
                ], 404);
            }

            $user = User::find($user_id);
            return response()->json([
                'user' => $user,
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
