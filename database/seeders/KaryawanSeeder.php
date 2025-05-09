<?php

namespace Database\Seeders;

use App\Models\Administrasi\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('karyawan')->truncate();
        Karyawan::create([
            'nama_lengkap'          => 'RIZKY ANDRIAWAN',
            'email'                 => 'rizkyandriawan33478@gmail.com',
            'nik'                   => '2015451256',
            'jabatan'               => 'IT',
            'tempat_lahir'          => 'JAKARTA',
            'tanggal_lahir'         => '2002-05-22',
            'alamat_domisili'       => 'Dsn. Kampung Baru Rt 002 Rw 13, Ds. Wonosari, Kec. Wonosari, Kab. Malang',
            'telepone'              => '081249418057',
            'jobdesk'               => 'Developer',
            'agama'                 => 'Islam',
            'ktp'                   => '1234567890123456',
            'status_pernikahan'     => false,
            'pendidikan_terakhir'   => 'Sekolah Menengah Kejuruan (SMK)',
            'tanggal_masuk'         => now(),
        ]);
    }
}
