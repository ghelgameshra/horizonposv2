<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = 'ref_jabatan';
        $data = ["Owner","Direktur Utama","Direktur Keuangan","Direktur Operasional","Direktur Pemasaran","Manajer","Manajer HRD","Manajer Keuangan","Manajer Operasional","Manajer Pemasaran","Supervisor","Staff"];

        DB::table($table)->truncate();

        foreach ($data as $value) {
            DB::table($table)->insert([
                'nama_jabatan'  => $value,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
