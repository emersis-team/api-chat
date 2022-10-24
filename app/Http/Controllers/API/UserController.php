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
    //APIs de Autogestión de usuarios
    public function getUserLoggedJWT()  //Esta API también se llama después del login contra el portal, si EXISTE el usuario -> devuelve el objeto usuario, SI NO existe -> devuelve 404
    {
        //Si el user_name que se envía por JWT existe -> devuelve el user_id, si no existe devuelve error 404
        $user_name = UserFromJWTController::getUserNameFromJWT();
        $user_id = UserFromJWTController::getUserIdFromJWT();

        try {
            //Chequea que exista el usuario con ese user_name o user_id (según corresponda)
            if($user_name){
                //echo "USER NAME: " . $user_name . "\n";
                $user = User::where('user_name',$user_name)->first();

            }else if($user_id){
                //echo "USER ID: " . $user_id;
                $user = User::where('id',$user_id)->first();
            }else{
                return response()->json([
                    'error' => "El token NO contiene el user_name ni el user_id.",
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

    public function updateUserLoggedJWT(Request $request)
    {
        $user_id = UserFromJWTController::getUserIdFromJWT();

        try {
            //Chequea que exista el usuario con ese user_id
            //echo "USER ID: " . $user_id;
            $user = User::find($user_id);

            if ($user != null) {
                //Chequea los campos de entrada
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => ['sometimes','string', 'max:255'],
                        'surname' => ['sometimes','string', 'max:255'],
                        'grade' => ['sometimes','nullable','string'],
                        'dni' => ['sometimes','integer'],
                        'location_id' => ['sometimes','integer', 'exists:locations,id'],
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $campos = $request;

                if($campos['name']){
                    $userUpdated['name'] = $campos['name'];
                }
                if($campos['surname']){
                    $userUpdated['surname'] = $campos['surname'];
                }
                if($campos['grade']){
                    $userUpdated['grade'] = $campos['grade'];
                }
                if($campos['dni']){
                    $userUpdated['dni'] = $campos['dni'];
                }
                if($campos['location_id']){
                    $userUpdated['location_id'] = $campos['location_id'];
                }

                //Actualiza el usuario
                User::where('id', $user_id)
                            ->update($userUpdated);

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

    //APIs de Administración de usuarios
    public function createUserJWT(Request $request)
    {
        try {

                //Chequea los campos de entrada
                $validator = Validator::make(
                $request->all(),
                  [
                    'user_name' => ['required','string', 'max:255'],
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

                //Chequea si existe el usuario con ese user_name, si existe no lo crea
                $user = User::where('user_name',$campos['user_name'])->first();

                if ($user == null) {

                    //Crear el usuario
                    $user = User::create([
                        'user_name' => $campos['user_name'],
                        'name' => $campos['name'],
                        'surname' => $campos['surname'],
                        'grade' => $campos['grade'],
                        'dni' => $campos['dni'],
                        'location_id' => $campos['location_id'],
                    ]);

                    if (!$user) {
                        throw new \Error('No se pudo crear el usuario.');
                    }
                }else{
                    echo "Ya existe el user con el user_name: " . $campos['user_name'] . "\n";
                }

            return response()->json([
                'user' => $user,
            ]);
        }

        catch (\Throwable $e) {
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserManagedJWT($user_id)
    {
        try {
            $user = User::where('id',$user_id)->first();

            echo "USER NAME: " . $user->name . "\n";
            echo "USER ID: " . $user->id . "\n";

            if ($user == null) {
                return response()->json([
                    'error' => "No exste el usuario.",
                ], 404);
           }

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

    public function updateUserManagedJWT($user_id, Request $request)
    {
        try {
            $user = User::where('id',$user_id)->first();
            if ($user != null) {
                //Chequea los campos de entrada
                $validator = Validator::make(
                    $request->all(),
                    [
                        'user_name' => ['sometimes','string', 'max:255'],
                        'name' => ['sometimes','string', 'max:255'],
                        'surname' => ['sometimes','string', 'max:255'],
                        'grade' => ['sometimes','nullable','string'],
                        'dni' => ['sometimes','integer'],
                        'location_id' => ['sometimes','integer', 'exists:locations,id'],
                    ]
                );

                if ($validator->fails()) {
                        return response()->json([
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $campos = $request;

                if($campos['user_name']){
                    $userUpdated['user_name'] = $campos['user_name'];
                }
                if($campos['name']){
                    $userUpdated['name'] = $campos['name'];
                }
                if($campos['surname']){
                    $userUpdated['surname'] = $campos['surname'];
                }
                if($campos['grade']){
                    $userUpdated['grade'] = $campos['grade'];
                }
                if($campos['dni']){
                    $userUpdated['dni'] = $campos['dni'];
                }
                if($campos['location_id']){
                    $userUpdated['location_id'] = $campos['location_id'];
                }

                //Actualiza el usuario
                User::where('id', $user_id)
                            ->update($userUpdated);

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
