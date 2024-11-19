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
            'email'     => 'administrator@email.com',
            'password'  => bcrypt('administrator@email.com')
        ]);

        User::create([
            'name'      => 'KASIR',
            'email'     => 'kasir@email.com',
            'password'  => bcrypt('password')
        ]);
    }
}
