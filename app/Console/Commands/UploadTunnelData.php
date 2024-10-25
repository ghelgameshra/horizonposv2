<?php

namespace App\Console\Commands;

use App\Models\TunnelLog;
use App\Models\UserApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UploadTunnelData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngrok:upload-tunnel-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload ngrok tunnel data to API periodically';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*
            Run test connection to API
        */
        $this->testConnection();

        /*
            Check is ngrok tunnel is running
        */
        $tunnels = $this->testTunnelApi();

        /*
            Login and get salt from API
        */
        $this->login();

        /*
            Get salt for encrypting JSON
        */
        $salt = $this->getSalt();
        $tunnelJson = $this->encryptString($tunnels, $salt);

        $this->upload($tunnelJson);
        TunnelLog::create([
            'data'      => $tunnels,
            'status'    => 'Success: Ngrok tunnel data uploaded successfully.',
            'api'       => env('APP_API')
        ]);
    }

    private function upload($data): void
    {
        $auth = $this->login();
        $upload = Http::withHeaders([
            'Authorization' => $auth
        ])->post(env('APP_API') . "/upload-tunnel-data", [
            'data'  => $data
        ]);

        if(!$upload->successful()){
            $this->fail($upload->body());
        }

        $this->info(env('APP_API') . ": " . $upload->body());
    }

    private function encryptString($plaintext, $key, $cipher = 'aes-256-cbc') {
        // Generate an initialization vector (IV) based on the selected cipher
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Encrypt the string using openssl_encrypt
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

        // Encode IV and ciphertext to make it safe for transmission
        return base64_encode($iv . $ciphertext);
    }

    private function login()
    {
        // Ambil user API dari database
        $userApi = UserApi::first();

        if($userApi->auth_token){
            $login = Http::withHeaders([
                'Authorization' => $userApi->auth_token
            ])->get(env('APP_API') . "/user");

            if($login->successful()){
                return $userApi->auth_token;
            }
        }

        // Kirim request POST untuk login
        $login = Http::post(env('APP_API') . "/user/login", [
            'nik'      => $userApi->nik,
            'password' => Crypt::decryptString($userApi->password),
        ]);

        /* Failed Login */
        if(!$login->successful()){
            $this->newLine();
            $this->error($login->body());
            $this->fail('Failed to get auth from API');
        }

        $auth = $login->json();
        $userApi->update([
            'auth_token'    => $auth['data']['token_type'] . " " . $auth['data']['access_token']
        ]);

        $this->info("Success login");

        return $userApi->auth_token; // return a token ex. Bearer 65|xxxxx from db
    }

    /*
        Test API connection
    */
    private function testConnection(): void
    {
        $conn = Http::get(env('APP_API'));
        $this->info("Starting connect to " . env('APP_API'));

        if(!$conn->successful()){
            TunnelLog::create([
                'data'      => $conn->json() === NULL ?? $conn->json(),
                'status'    => 'Error: Unable connect to API.',
                'api'       => env('APP_API')
            ]);
            $this->fail('Unable connect to ' . env('APP_API'));
        }
    }

    /*
        Test and get tunnel data to encoded JSON
    */
    private function testTunnelApi()
    {
        $conn = Http::get(env('APP_TUNNELS'));
        $this->info("Starting check ngrok service on " . env('APP_TUNNELS'));

        if(!$conn->successful()){
            TunnelLog::create([
                'data'      => $conn->body(),
                'status'    => 'Error: Unable connect to tunnels API.',
                'api'       => env('APP_TUNNELS')
            ]);
            $this->fail('Unable connect to ' . env('APP_TUNNELS'));
        }

        if(empty($conn)) $this->fail('Unable retrieve data from ' . env('APP_TUNNELS'));
        $this->info("Starting retrieve data from " . env('APP_TUNNELS'));

        $toko = Cache::remember('toko_first', 60, function () {
            return DB::table('toko')->first();
        });

        $data = $conn->json();
        $tunnels = $data['tunnels'];
        foreach ($tunnels as $key => $value) {
            $tunnels[$key]['app_name'] = env('APP_NAME');
            $tunnels[$key]['company_name'] = $toko->nama_perusahaan;
            $tunnels[$key]['period'] = now()->format('Y-m-d');
            $tunnels[$key]['time'] = now()->format('H:i:s');

            $tunnels[$key]['db_username'] = $value['name'] === 'mysql' ? env('DB_USERNAME') : '';
            $tunnels[$key]['db_password'] = $value['name'] === 'mysql' ? env('DB_PASSWORD') : '';
            $tunnels[$key]['db_database'] = $value['name'] === 'mysql' ? env('DB_DATABASE') : '';
            $tunnels[$key]['detail'] = $value;
        }

        return json_encode($tunnels);
    }

    private function getSalt()
    {
        $auth = $this->login();
        $request = Http::withHeaders([
            'Authorization' => $auth
        ])->get(env('APP_API') . "/password");

        if(!$request->successful()) $this->fail($request->body());

        $jsonData = $request->json();

        $userApi = UserApi::first();
        $userApi->update([
            'salt'  => $jsonData['data']['salt']
        ]);

        return $userApi->salt;
    }

}
