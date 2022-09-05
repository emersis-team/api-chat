<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class jwt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:jwt';

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

    public function generate_jwt($headers, $payload, $secret = 'secret') {

        $headers_encoded = rtrim(strtr(base64_encode(json_encode($headers)), '+/', '-_'), '=');

        $payload_encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = rtrim(strtr(base64_encode(json_encode($signature)), '+/', '-_'), '=');

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

        return $jwt;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $headers = array('alg'=>'HS256','typ'=>'JWT');
        $payload = array('sub'=>'1234567890','client'=>'CLIENT_2_SECRET', 'user_id'=>1, 'admin'=>true, 'exp'=>(time() + 3600));

        $headers_encoded = rtrim(strtr(base64_encode(json_encode($headers)), '+/', '-_'), '=');

        $payload_encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", 'client_2', true);
        $signature_encoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

        echo $jwt . "\n";
        return 0;
    }
}
