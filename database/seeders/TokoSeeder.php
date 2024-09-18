<?php

namespace Database\Seeders;

use App\Models\Administrasi\Toko;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('toko')->delete();
        Toko::create([
            'nama_perusahaan'   => "HORIZON TECH SOLUSINDO",
            'email'             => "rizkyandriawan33478@gmail.com",
            'telepone'          => "081249418057",
            'whatsapp'          => "081249418057",
            'instagram'         => "@ghelgameshra",
            'facebook'          => "@ghelgameshra",
            'tiktok'            => "@ghelgameshra",
            'alamat_lengkap'    => "Dsn. Kampung Baru Rt 002 Rw 013, Ds. Wonosari, Kec. Wonosari, Kab. Malang - Jawa Timur",
            'qr_wa'             => 'default/images/qr_wa.png'
        ]);
    }
}
