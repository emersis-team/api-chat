<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Location;
use Illuminate\Database\QueryException;

class LocationController extends Controller
{
    public function getLocations()
    {
        $locations = Location::select(['id', 'name','address','contact_info'])->orderBy('name', 'asc')->get();
        return response()->json(['locations' => $locations]);

    }

    public function getLocation(Location $location)
    {
        return response()->json($location);
    }

    public function createLocation(Request $request)
    {
        try {

            //Chequea los campos de entrada
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required','string', 'max:255'],
                    'address' => ['sometimes','string', 'max:255'],
                    'contact_info' => ['sometimes','string', 'max:255'],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                 'errors' => $validator->errors(),
                ], 422);
            }

            $campos = $request;

            //Chequea si existe la location con ese name, si existe no lo crea
            $location = Location::where('name',$campos['name'])->first();

            if ($location == null) {

                //Crear el usuario
                $location = Location::create([
                    'name' => $campos['name'],
                    'address' => $campos['address'],
                    'contact_info' => $campos['contact_info'],
                ]);

                if (!$location) {
                    throw new \Error('No se pudo crear la location.');
                }
            }else{
                return response()->json([
                    'error' => "Ya existe la location con el name: " . $campos['name'],
                ], 422);
            }

            return response()->json([
                'location' => $location,
            ]);
        }

        catch (\Throwable $e) {
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateLocation(Location $location, Request $request)
    {
        try {
            $location_id = $location->id;

            //Chequea los campos de entrada
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['sometimes','string', 'max:255'],
                    'address' => ['sometimes','string', 'max:255'],
                    'contact_info' => ['sometimes','string', 'max:255'],
                ]
            );

            if ($validator->fails()) {
                    return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            $campos = $request;
            $locationUpdated = array();

            //Si se envía un valor de  name -> Chequea si existe la location con ese name, si existe no lo crea
            if($campos['name']){
                $location = Location::where('name',$campos['name'])
                                  ->where('id','<>', $location_id)
                                  ->first();
            
                if($location != null){
                    return response()->json([
                        'error' => "Ya existe la location con el name: " . $campos['name'],
                    ], 422);
                }

                $locationUpdated['name'] = $campos['name'];
            }

            if($campos['address']){
                $locationUpdated['address'] = $campos['address'];
            }
            
            if($campos['contact_info']){
                $locationUpdated['contact_info'] = $campos['contact_info'];
            }

            if ($campos['name'] || $campos['address'] || $campos['contact_info']) {

                //Actualiza la location
                Location::where('id', $location_id)
                        ->update($locationUpdated);
            }

            $location = Location::find($location_id);

            return response()->json($location);
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
