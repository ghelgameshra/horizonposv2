<?php

namespace Database\Seeders;

use App\Models\UserApi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users_api')->truncate();
        UserApi::create([
            'email'     => 'ghelgameshra3347@gmail.com',
            'nik'       => '2015451256',
            'password'  => Crypt::encryptString('Gh3lgameshra.')
        ]);
    }
}
