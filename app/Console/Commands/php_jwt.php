<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class php_jwt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:php_jwt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $key = 'CIDESO';

        $payload = [
            //'user_name' => NULL,
            'user_name' => 'ebuiatti',
            //'user_id' => NULL,
            'user_id' => 1

        ];

        //$payload = ['Hola, mundo!'];

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, 'HS512');
        $decoded = JWT::decode($jwt, new Key($key, 'HS512'));

        print_r($decoded);

        /*
        NOTE: This will now be an object instead of an associative array. To get
        an associative array, you will need to cast it as such:
        */

        $decoded_array = (array) $decoded;

        print_r($decoded_array);

        /**
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
         */

        // JWT::$leeway = 60; // $leeway in seconds
        // $decoded = JWT::decode($jwt, new Key($key, 'HS512'));

        // print_r($decoded);

        //JWT generada con JAVA
        //$jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJIb2xhLCBtdW5kbyEifQ.801KrUFy9Drm2YDwCNRV7HybCCmO0bOy90ySZYjSSWszR3S_bPkF5dMMASVZ_f-D9ruZ16ih9L-oMhbtaUZ_zw";
        print_r($jwt . "\n\n");



        //Valido JWT
        $key_2 = "CIDESO";
        $payload = JWT::decode($jwt, new Key($key_2, 'HS512'));

        $payload_array = (array) $payload;
        $jwt2 = JWT::encode($payload_array, $key_2, 'HS512');

        print_r($payload_array);
        print_r("\n\n");

        print_r($jwt2 . "\n\n");

        return 0;
    }
}
