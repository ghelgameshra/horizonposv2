<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'RIZKY ANDRIAWAN',
            'email'     => 'rizkyandriawan33478@gmail.com',
            'job'       => 'DEV/SUPPORT',
            'nik'       => '2015451256',
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
            'password'  => bcrypt('kasir@email.com')
        ]);

        User::create([
            'name'      => 'OPERATOR',
            'email'     => 'operator@email.com',
            'password'  => bcrypt('kasir@email.com')
        ]);
    }
}
