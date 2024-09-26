<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis_pengeluaran = ["Gaji Karyawan","Sewa Kantor","Listrik & Air","Perlengkapan Kantor","Internet & Telepon","Transportasi","Promosi & Pemasaran","Pemeliharaan & Perbaikan","Asuransi","Pajak & Izin"];

        $table = 'ref_jenis_pengeluaran';

        DB::table($table)->truncate();
        foreach ($jenis_pengeluaran as $key => $value) {
            DB::table($table)->insert(array_merge([
                'nama_pengeluaran'  => $value
            ], [
                'created_at'    => now(),
                'updated_at'    => now(),
            ]));
        }

    }
}
