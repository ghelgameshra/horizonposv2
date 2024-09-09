<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UkuranSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ukuran = ['PCS', 'CM', 'ROLL'];
        foreach ($ukuran as $value) {
            DB::table('ref_jenis_ukuran')->insert([
                'nama_jenis'    => $value,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $satuan = ['UNIT', 'LUAS', 'KELILING'];
        foreach ($satuan as $value) {
            DB::table('ref_satuan')->insert([
                'nama_satuan'    => $value,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
