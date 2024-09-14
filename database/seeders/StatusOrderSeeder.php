<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['DALAM ANTRIAN','CETAK','FINISHING','PACKING','PESANAN DIAMBIL'];
        $table = 'ref_status_order';

        echo "[*] Truncate tabel $table\n";
        DB::table($table)->truncate();

        echo "[*] Mulai insert data ke tabel $table\n\n";
        foreach ($data as $value) {
            echo "[+] Insert data `$value` ke tabel $table\n";
            DB::table($table)->insert([
                'nama_status'   => $value,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        echo "\n[*] Selesai insert data status order\n";
    }
}
