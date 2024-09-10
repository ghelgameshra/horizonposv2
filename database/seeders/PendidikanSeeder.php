<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenjangPendidikan = [
            [
                "Jenjang_Pendidikan" => "Pendidikan Anak Usia Dini (PAUD)",
                "Keterangan" => "Pendidikan sebelum masuk sekolah dasar"
            ],
            [
                "Jenjang_Pendidikan" => "Taman Kanak-Kanak (TK)",
                "Keterangan" => "Pendidikan formal untuk anak usia 4-6 tahun"
            ],
            [
                "Jenjang_Pendidikan" => "Sekolah Dasar (SD)",
                "Keterangan" => "Pendidikan formal dasar selama 6 tahun"
            ],
            [
                "Jenjang_Pendidikan" => "Sekolah Menengah Pertama (SMP)",
                "Keterangan" => "Pendidikan menengah pertama selama 3 tahun"
            ],
            [
                "Jenjang_Pendidikan" => "Sekolah Menengah Atas (SMA)",
                "Keterangan" => "Pendidikan menengah atas selama 3 tahun, jurusan IPA, IPS, atau Bahasa"
            ],
            [
                "Jenjang_Pendidikan" => "Sekolah Menengah Kejuruan (SMK)",
                "Keterangan" => "Pendidikan menengah kejuruan selama 3 tahun"
            ],
            [
                "Jenjang_Pendidikan" => "Diploma (D1, D2, D3, D4)",
                "Keterangan" => "Pendidikan vokasi pada perguruan tinggi"
            ],
            [
                "Jenjang_Pendidikan" => "Sarjana (S1)",
                "Keterangan" => "Pendidikan akademik setara strata 1"
            ],
            [
                "Jenjang_Pendidikan" => "Magister (S2)",
                "Keterangan" => "Pendidikan akademik setara strata 2"
            ],
            [
                "Jenjang_Pendidikan" => "Doktoral (S3)",
                "Keterangan" => "Pendidikan akademik setara strata 3"
            ]
        ];

        $table = 'ref_pendidikan_terakhir';
        DB::table($table)->truncate();
        foreach ($jenjangPendidikan as $value) {
            DB::table($table)->insert([
                'nama_jenjang'  => $value['Jenjang_Pendidikan'],
                'keterangan'    => $value['Keterangan'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
