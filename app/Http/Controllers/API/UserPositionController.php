<?php

namespace App\Http\Controllers\API;

use App\Events\NewPositionEvent;
use App\Models\UserContact;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserPositionController extends Controller
{
   
    public function getUserPositions($user_id)
    { 
        //$user = Auth::user();

        $user_id =intval($user_id);
        try {
            //Chequea que exista el usuario
            $user = User::find($user_id);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            $userPositions = $user->positions;

            return response()->json([
                'user_id' => $user->id,
                'positions' => $userPositions,
            ]);

        }

        catch (\Throwable $e) {

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }

    }

    public function getLastUserPosition($user_id)
    { 
         //$user = Auth::user();

         $user_id =intval($user_id);
         try {
             //Chequea que exista el usuario
             $user = User::find($user_id);
 
             if ($user == null) {
                 throw new AccessDeniedHttpException(__('No existe el usuario.'));
             }
 
            $userLastPosition = $user->positions->first();

            return response()->json([
                'user_id' => $user->id,
                'positions' => $userLastPosition,
            ]);
         }

         catch (\Throwable $e) {

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function createUserPosition(Request $request)
    {
        $user = Auth::user();

        try {
            //Chequea los campos de entrada
            $campos = $request->validate([
                'user_id' => ['required','integer', 'exists:users,id'],
                'lat' => ['required','numeric'],
                'lon' => ['required','numeric'],
                'alt' => ['required','numeric'],
            ]);

            $user = User::find($campos['user_id']);
            //var_dump($user);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            //Crea la posición del usuario
            $user_position = UserPosition::create([
                'user_id' => $user->id,
                'lat' => $campos['lat'],
                'lon' => $campos['lon'],
                'alt' => $campos['alt']
            ]);
            
            if (!$user_position) {
                throw new \Error('No se pudo crear la posición actual del usuario.');
            }

            //Se buscan los contactos del usuario
            //Cuando se implemente OAuth, los contactos los debe enviar el FRONT
            $contacts = User::where('id','<>', $user->id)->get('id');

            //Se lanza evento de NewPositioEvent
            //broadcast(new NewPositionEvent($user_position, $contacts));

            return response()->json([
                            'status' => 200,
                            'message' => 'Creación de la posición del usuario, realizada con éxito',
                            'user_position_id' => $user_position->id,
                            'lat' => $user_position->lat,
                            'lon' => $user_position->lon,
                            'alt' => $user_position->alt

            ]);
        }
        
        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {
            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function getContactsPositions($user_id)
    {
        //$user = Auth::user();

        $user_id =intval($user_id);
        try {
            //Chequea que exista el usuario
            $user = User::find($user_id);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            $userContactsUser = array();

            $userContacts = UserContact::where('user_id', $user_id)
                                        ->where('contact_type', 'App\\User')
                                        ->get();

            foreach ($userContacts as $i => $userContact) {

                //var_dump("USER_CONTACT_IND: " . $userContact);
                $userContactsUser[$i]['id'] = $userContact['contact_id'];
                $userContactsUser[$i]['positions'] = $userContact->positions;

            }

            return response()->json([
                'user' => $user_id,
                'user_positions' => $user->positions,
                'user_contacts_positions' => $userContactsUser,
            ]);
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
