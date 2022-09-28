<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserFromJWTController extends Controller
{
    //static public $user_id;
    static public function getUserId()
    {
        //INICIA extracción de userId desde el TOKEN enviado en el header
        $jwt = "";

        foreach (getallheaders() as $name => $value) {
            //echo "$name: $value\n";
            if($name == "Authorization"){
                $jwt = substr($value, 7); //Se extrae 'Bearer ' y nos quedamos con el token
                //echo $jwt . "\n";
                break;
            }
        }

        // split the jwt
        $tokenParts = explode('.', $jwt);
        $payload = base64_decode($tokenParts[1]);

        //echo $payload . "\n";

        $user_id = json_decode($payload)->user_id;
        //echo "USER_ID: " . $user_id . "\n";

        //FINALIZA extracción de userId desde el TOKEN

        $user_id = intval($user_id);
        return $user_id;
    }

    static public function getUserName()
    {
        //INICIA extracción de userName desde el TOKEN enviado en el header
        $jwt = "";

        foreach (getallheaders() as $name => $value) {
            //echo "$name: $value\n";
            if($name == "Authorization"){
                $jwt = substr($value, 7); //Se extrae 'Bearer ' y nos quedamos con el token
                //echo $jwt . "\n";
                break;
            }
        }

        // split the jwt
        $tokenParts = explode('.', $jwt);
        $payload = base64_decode($tokenParts[1]);

        //echo $payload . "\n";

        $user_name = json_decode($payload)->user_name;
        //echo "USER_ID: " . $user_name . "\n";

        //FINALIZA extracción de userName desde el TOKEN

        return $user_name;
    }

}
