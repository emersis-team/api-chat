<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Group;
use Illuminate\Database\QueryException;

class GroupController extends Controller
{
    public function getGroups()
    {
        $groups = Group::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return response()->json(['groups' => $groups]);

    }

    public function getGroup(Group $group)
    {
        return response()->json($group);
    }

    public function createGroup(Request $request)
    {
        try {

            //Chequea los campos de entrada
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required','string', 'max:255'],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                 'errors' => $validator->errors(),
                ], 422);
            }

            $campos = $request;

            //Chequea si existe el group con ese name, si existe no lo crea
            $group = Group::where('name',$campos['name'])->first();

            if ($group == null) {

                //Crear el usuario
                $group = Group::create([
                    'name' => $campos['name'],
                ]);

                if (!$group) {
                    throw new \Error('No se pudo crear el group.');
                }
            }else{
                return response()->json([
                    'error' => "Ya existe el group con el nombre: " . $campos['name'],
                ], 422);
            }

            return response()->json([
                'group' => $group,
            ]);
        }

        catch (\Throwable $e) {
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateGroup(Group $group, Request $request)
    {
        try {
            $group_id = $group->id;

            //Chequea los campos de entrada
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['sometimes','string', 'max:255'],
                ]
            );

            if ($validator->fails()) {
                    return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            $campos = $request;
            $groupUpdated = array();

            if($campos['name']){
                //Si se envía el name -> Chequea si existe otro group con ese name, si existe no lo actualiza
                $group = Group::where('name',$campos['name'])
                                ->where('id','<>', $group_id)
                            ->first();

                if ($group != null) {
                    return response()->json([
                        'error' => "Ya existe el group con el nombre: " . $campos['name'],
                    ], 422);
                }
                $groupUpdated['name'] = $campos['name'];

                //Actualiza el usuario
                Group::where('id', $group_id)
                        ->update($groupUpdated);
            }

            $group = Group::find($group_id);

            return response()->json([
                'group' => $group,
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
