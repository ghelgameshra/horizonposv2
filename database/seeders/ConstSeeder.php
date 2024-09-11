<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'MMBR' => [
                'keterangan'    => 'member sequence',
                'docno'         => 1
            ],
            'AUPR' => [
                'keterangan'    => 'otomatis cetak struk setelah selesai kasiran',
                'docno'         => 0
            ],
            'CKBJ' => [
                'keterangan'    => 'proses cek plu bisa jual untuk semua plu',
                'docno'         => 1
            ]
        ];

        DB::table('const')->truncate();

        foreach ($data as $key => $value) {
            DB::table('const')->insert([
                'key'           => $key,
                'docno'         => $value['docno'],
                'keterangan'    => $value['keterangan'],
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }
    }
}
