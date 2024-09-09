<?php

namespace Database\Seeders;

use App\Models\Administrasi\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('const')->where('key', 'MMBR')->update([
            'docno' => 2
        ]);
        DB::table('member')->delete();

        Member::create([
            'kode_member'   => "MBR" . now()->format('ymd') . str_pad(1, 4, '0', STR_PAD_LEFT),
            'nama_lengkap'  => 'TEST MEMBER',
            'telepone'      => '1234567890',
            'alamat_lengkap'=> fake()->address(),
            'email'         => fake()->email()
        ]);
    }
}
