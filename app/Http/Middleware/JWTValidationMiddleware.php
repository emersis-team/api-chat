<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        $secret = 'CIDESO';
        //echo "SECRET: " . $secret . "\n";

        $jwt = $request->headers->get("Authorization");
        //$client = "CLIENT_2_SECRET";
        //$client = $request->headers->get("client");

        //  echo "Authorization" . $request->headers->get("Authorization") . "\n";
        //  echo "CLIENTE: " . $request->headers->get("client") . "\n";

        $jwt = substr($jwt, 7); //Se extrae 'Bearer ' y nos quedamos con el token

        //INICIA Validación de JWT

        $payload = JWT::decode($jwt, new Key($secret, 'HS512'));
        //TODO Ver tema del error que tira si el token NO es válido. Hoy la libería devuelve 500 - Internal Server Error

        //$payload_array = (array) $payload;
        //$jwt_validado = JWT::encode($payload_array, $secret, 'HS512');

        // print_r($payload_array);
        // print_r("\n\n");
        // print_r($jwt_validado . "\n\n");

        //FINALIZA validación de TOKEN

        return $next($request);
    }
}
