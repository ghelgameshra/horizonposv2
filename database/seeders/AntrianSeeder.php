<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AntrianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'ATRD' => [
                'keterangan'    => 'antrian customer desain',
                'docno'         => 0,
                'pengucapan'    => ''
            ],
            'ATPD' => [
                'keterangan'    => 'antrian customer terpanggil desain',
                'docno'         => 0,
                'pengucapan'    => 'nomor antrian desain {noAntri} silahkan menuju ke meja desainer nomor {noMeja}'
            ],
            'ATRS' => [
                'keterangan'    => 'antrian customer siap cetak',
                'docno'         => 0,
                'pengucapan'    => ''
            ],
            'ATPS' => [
                'keterangan'    => 'antrian customer terpanggil siap cetak',
                'docno'         => 0,
                'pengucapan'    => 'nomor antrian siap cetak {noAntri} silahkan menuju ke meja desainer nomor {noMeja}'
            ]
        ];

        DB::table('antrian')->truncate();
        foreach ($data as $key => $value) {
            DB::table('antrian')->insert([
                'key'           => $key,
                'periode'       => now(),
                'keterangan'    => $value['keterangan'],
                'pengucapan'    => $value['pengucapan'],
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }
    }
}
