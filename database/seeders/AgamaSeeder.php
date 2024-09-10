<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agama = [
            "Islam",
            "Kristen Protestan",
            "Kristen Katolik",
            "Hindu",
            "Buddha",
            "Konghucu"
        ];

        $table = 'ref_agama';
        DB::table($table)->truncate();

        foreach ($agama as $value) {
            DB::table($table)->insert([
                'nama_agama'    => $value,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
