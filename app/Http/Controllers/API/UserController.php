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

use Illuminate\Database\QueryException;
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
                $userUpdated = array();

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

    public function createUserJWT(Request $request)
    {
        $user_name = UserFromJWTController::getUserNameFromJWT();

        try {
            //Chequea que el user_name NO exista ya en la BD
            if($user_name){
                //echo "USER NAME: " . $user_name . "\n";
                $user = User::where('user_name',$user_name)->first();

                if($user != null){
                    return response()->json([
                        'error' => "Ya existe el usuario con el user_name: " . $user_name,
                    ], 422);
                }else{
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
                            'user_name' => $user_name,
                            'name' => $campos['name'],
                            'surname' => $campos['surname'],
                            'grade' => $campos['grade'],
                            'dni' => $campos['dni'],
                            'location_id' => $campos['location_id'],
                    ]);

                    if (!$user) {
                            throw new \Error('No se pudo crear el usuario.');
                    }
                }

            }else{
                return response()->json([
                    'error' => "El token NO contiene el user_name.",
                ], 422);
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

    //APIs de Administración de usuarios
    public function getUserManagedJWT($user_id)
    {
        try {
            $user = User::where('id',$user_id)->first();

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

    public function updateUserManagedJWT($user_id, Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('id',$user_id)->first();
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
                        'admin' => ['sometimes','boolean'],
                        'contacts_create' => ['sometimes', 'array'],
                        'contacts_create.*' => ['integer', 'exists:users,id'],
                        'contacts_delete' => ['sometimes', 'array'],
                        'contacts_delete.*' => ['integer', 'exists:users,id'],
                        'groups_create' => ['sometimes', 'array'],
                        'groups_create.*' => ['integer', 'exists:groups,id'],
                        'groups_delete' => ['sometimes', 'array'],
                        'groups_delete.*' => ['integer', 'exists:groups,id'],
                    ]
                );

                if ($validator->fails()) {
                        return response()->json([
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $campos = $request;
                $userUpdated = array();

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
                if(isset($campos['admin'])){
                    $userUpdated['admin'] = $campos['admin'];
                }

                //ALTA y BAJA de Contactos y grupos 
                if($campos['contacts_create']){
                    foreach($campos['contacts_create'] as $h => $contact_create){
                        if($user_id <> $contact_create){
                            $contacts_create[$h]['user_id'] = $user_id;
                            $contacts_create[$h]['contact_id'] = $contact_create;
                            $contacts_create[$h]['contact_type'] = "App\\User";

                            $contact_creation = UserContact::where('contact_type' , "App\User")
                                                        ->where('user_id', $user_id)
                                                        ->where('contact_id' , $contact_create) 
                                                         ->first();

                            echo("HOLA: " . $contact_creation . "CHAU");
                        }
                    }
                }

                if($campos['contacts_delete']){
                    foreach($campos['contacts_delete'] as $i => $contact_delete){
                        $contacts_delete[$i]['user_id'] = $user_id;
                        $contacts_delete[$i]['contact_id'] = $contact_delete;
                        $contacts_delete[$i]['contact_type'] = "App\User";
                    }
                }

                if($campos['groups_create']){
                    foreach($campos['groups_create'] as $j => $group_create){
                        $groups_create[$j]['user_id'] = $user_id;
                        $groups_create[$j]['contact_id'] = $group_create;
                        $groups_create[$j]['contact_type'] = "App\Models\Group";
                    }
                }

                if($campos['groups_delete']){
                    foreach($campos['groups_delete'] as $k => $group_delete){
                        $groups_delete[$k]['user_id'] = $user_id;
                        $groups_delete[$k]['contact_id'] = $group_delete;
                        $groups_delete[$k]['contact_type'] = "App\Models\Group";
                    }
                }

                //Actualiza el usuario
                User::where('id', $user_id)
                            ->update($userUpdated);

            }else{
                return response()->json([
                    'error' => "No exste el usuario.",
                ], 404);
            }

            DB::commit();

            $user = User::find($user_id);
            return response()->json([
                'user' => $user,
                'contacts' => $contacts_create,
                'contacts_delete' => $contacts_delete,
                'groups' => $groups_create,
                'groups_delete' => $groups_delete,
            ]);

        }

        catch (QueryException $e) {
            DB::rollBack();
            throw new \Error('Hay un Error SQL');
        }

        catch (\Throwable $e) {
            DB::rollBack();
            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }

    }
}
