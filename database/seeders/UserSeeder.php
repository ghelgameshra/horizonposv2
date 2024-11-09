<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'RIZKY ANDRIAWAN',
            'email'     => 'ghelgameshra3347@gmail.com',
            'password'  => bcrypt('gh3lgameshra')
        ]);

        User::create([
            'name'      => 'ADMINISTRATOR',
            'email'     => 'administrator@gmail.com',
            'password'  => bcrypt('administrator@gmail.com')
        ]);

        User::create([
            'name'      => 'KASIR1',
            'email'     => 'kasir1@gmail.com',
            'password'  => bcrypt('password')
        ]);

        User::create([
            'name'      => 'KASIR2',
            'email'     => 'kasir2@gmail.com',
            'password'  => bcrypt('password')
        ]);
    }
}
