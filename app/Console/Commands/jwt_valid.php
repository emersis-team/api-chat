<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class jwt_valid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:is_valid';

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
        $jwt = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwiY2xpZW50IjoiQ0xJRU5UXzFfU0VDUkVUIiwidXNlcl9pZCI6MSwiYWRtaW4iOnRydWUsImV4cCI6MTY2MTg5OTY5MH0.hCmlr4jCAxQ_8cYFfzpbGcG3ZCk-EMIY9de0Ee3fIPU";

        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        echo $header . "\n";
        echo $payload . "\n";
        echo $signature_provided . "\n";

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        $expiration = json_decode($payload)->exp;

        if($expiration - time() < 0){
            $is_token_expired = 1;
        }else {
            $is_token_expired = 0;
        }


        echo $expiration . "\n";
        echo time() . "\n";
        echo $is_token_expired . "\n";

        // build a signature based on the header and payload using the secret
        $base64_url_header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64_url_payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, 'secreto', true);
        $base64_url_signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        echo $base64_url_signature . "\n";
        echo $signature_provided . "\n";

        // verify it matches the signature provided in the jwt
        if($base64_url_signature === $signature_provided){
            $is_signature_valid = 1;
        }else{
            $is_signature_valid = 0;
        }

        echo $is_signature_valid . "\n";

        if ($is_token_expired || !$is_signature_valid) {
            echo "NO es valido". "\n";
            return FALSE;
        } else {
            echo "ES valido". "\n";
            return TRUE;
        }
    }
}
