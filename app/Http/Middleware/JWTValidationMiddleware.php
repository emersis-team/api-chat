<?php

namespace App\Http\Middleware;

use Closure;

class JWTValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
         //INICIA Validación del TOKEN enviado en el header
         $jwt = "";
         $client = "";

         $jwt = $request->headers->get("Authorization");
         $client = "CLIENT_2_SECRET";
         //$client = $request->headers->get("client");

        //  echo "Authorization" . $request->headers->get("Authorization") . "\n";
        //  echo "CLIENTE: " . $request->headers->get("client") . "\n";

         $jwt = substr($jwt, 7); //Se extrae 'Bearer ' y nos quedamos con el token
 
         // split the jwt
         $tokenParts = explode('.', $jwt);
         $header = base64_decode($tokenParts[0]);
         $payload = base64_decode($tokenParts[1]);
         $signature_provided = $tokenParts[2];
 
         //echo $header . "\n";
         //echo $payload . "\n";
         //echo $signature_provided . "\n";
 
         //Se consulta qué CLIENT es el que está queriendo acceder a la API, para extraer la SECRET del .env
 
         $secret = getenv($client);
         //echo "SECRET: " . $secret . "\n";
 
         // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
         $expiration = json_decode($payload)->exp;
 
         if($expiration - time() < 0){
             $is_token_expired = 1;
             echo $is_token_expired . " El token EXPIRÓ\n";
             return abort(403);
         }else {
             $is_token_expired = 0;
             echo $is_token_expired . " El token NO EXPIRÓ\n";;
         }
 
         // build a signature based on the header and payload using the secret
         $base64_url_header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
         $base64_url_payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
         $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
         $base64_url_signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
 
         // verify it matches the signature provided in the jwt
         if($base64_url_signature === $signature_provided){
             $is_signature_valid = 1;
             echo $is_signature_valid . " La FIRMA es Válida\n";
         }else{
             $is_signature_valid = 0;
             echo $is_signature_valid . " La FIRMA NO es Válida\n";
             return abort(403);
         }
 
         //FINALIZA validación de TOKEN

        return $next($request);
    }
}
