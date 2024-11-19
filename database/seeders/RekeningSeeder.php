<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ref_rekening')->truncate();
        DB::table('ref_rekening')->insert([
            'nama_bank'         => 'BCA',
            'nomor_rekening'    => '1234567890',
            'nama_pemilik'      => 'RIZKY ANDRIAWAN',
            'default'           => true
        ]);
    }
}
