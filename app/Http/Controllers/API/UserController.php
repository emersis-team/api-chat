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
            $groups = UserContact::select('contact_id as group_id')
                                   ->where('user_id',$user_id)
                                   ->where('contact_type','App\Models\Group')
                                   ->get();

            $contacts = UserContact::where('user_id',$user_id)
                                   ->where('contact_type','App\User')
                                   ->get('contact_id');

            // echo "USER NAME: " . $user->name . "\n";
            // echo "USER ID: " . $user->id . "\n";

            if ($user == null) {
                return response()->json([
                    'error' => "No exste el usuario.",
                ], 404);
           }

            return response()->json([
                'user' => $user,
                'groups' => $groups,
                'contacts' => $contacts,
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
                        'contacts' => ['sometimes', 'array'],
                        'contacts.*' => ['integer', 'exists:users,id'],
                        'groups' => ['sometimes', 'array'],
                        'groups.*' => ['integer', 'exists:groups,id'],
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

                //Contactos y grupos

                $contacts = array();
                $groups = array();
                $contacts_id = array();
                $groups_id = array();

                if($campos['contacts']){
                    foreach($campos['contacts'] as $h => $contact_id){
                        if($user_id <> $contact_id){
                            $contacts_id[$h] = $contact_id;

                            $contacts[$h]['user_id'] = $user_id;
                            $contacts[$h]['contact_id'] = $contact_id;
                            $contacts[$h]['contact_type'] = "App\\User";

                            $contact_exists = UserContact::where('contact_type' , "App\User")
                                                        ->where('user_id', $user_id)
                                                        ->where('contact_id' , $contact_id)
                                                         ->first();

                            if(!$contact_exists){
                                 $contact_create = UserContact::create([
                                    'user_id' => $user_id,
                                    'contact_id' => $contact_id,
                                    'contact_type' => "App\User",
                                ]);

                                $contact_create_vice = UserContact::create([
                                    'user_id' => $contact_id,
                                    'contact_id' => $user_id,
                                    'contact_type' => "App\User",
                                ]);
                            }
                        }
                    }

                    //Borra los contactos anteriores que NO están especificados ahora y viceversa
                    $contact_delete = UserContact::where('contact_type' , "App\User")
                    ->where('user_id', $user_id)
                    ->whereNotIN('contact_id' , $contacts_id)
                     ->delete();

                     $contact_delete_vice = UserContact::where('contact_type' , "App\User")
                    ->where('contact_id', $user_id)
                    ->whereNotIN('user_id' , $contacts_id)
                     ->delete();
                }else{ //NO se envió el array de contacts -> NO tiene asociado contactos

                    //Borra los contactos anteriores y viceversa
                    $contact_delete = UserContact::where('contact_type' , "App\User")
                    ->where('user_id', $user_id)
                    ->delete();

                    $contact_delete_vice = UserContact::where('contact_type' , "App\User")
                    ->where('contact_id', $user_id)
                     ->delete();

                }

                if($campos['groups']){
                    foreach($campos['groups'] as $j => $group_id){
                        $groups_id[$j] = $group_id;

                        $groups[$j]['user_id'] = $user_id;
                        $groups[$j]['contact_id'] = $group_id;
                        $groups[$j]['contact_type'] = "App\Models\Group";

                        $group_exists = UserContact::where('contact_type' , "App\Models\Group")
                                                        ->where('user_id', $user_id)
                                                        ->where('contact_id' , $group_id)
                                                         ->first();

                        if(!$group_exists){
                            $group_create = UserContact::create([
                                            'user_id' => $user_id,
                                            'contact_id' => $group_id,
                                            'contact_type' => "App\Models\Group",
                            ]);
                        }
                    }

                    //Borra los grupos anteriores que NO están especificados ahora
                    $contact_delete = UserContact::where('contact_type' , "App\Models\Group")
                                                    ->where('user_id', $user_id)
                                                    ->whereNotIN('contact_id' , $groups_id)
                                                    ->delete();

                }else{ //NO se envió el array de groups -> NO tiene asociado grupos

                    //Borra los grupos anteriores
                    $contact_delete = UserContact::where('contact_type' , "App\Models\Group")
                                                    ->where('user_id', $user_id)
                                                    ->delete();

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
                'contacts' => $contacts_id,
                'groups' => $groups_id,
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

    public function getUsersJWT($location_id = 0)
    {
        try {

            if($location_id > 0){
                $users = User::where('location_id',$location_id)
                                ->orderby('user_name', 'asc')
                                ->get();
            }else{
                $users = User::orderby('user_name', 'asc')->get();
            }

            return response()->json([
                'users' => $users,
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
