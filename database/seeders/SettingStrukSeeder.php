<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingStrukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $time = [
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        $settings = [
            'LOGS' => 'logo struk',
            'HEDS' => 'header struk',
            'ISIS' => 'isi struk',
            'FOOS' => 'footer struk',
            'PESS' => 'pesan struk',
            'QRSK' => 'qr struk',
        ];

        DB::table('setting_struk')->truncate();

        foreach ($settings as $key => $value) {
            DB::table('setting_struk')->insert(array_merge([
                'nama_setting'  => $value,
                'key'           => $key
            ], $time));
        }

        DB::table('setting_pesan_struk')->truncate();
        $pesan = [
            'periksa kembali pesanan anda.',
            'kesalahan pemesanan karena kelalaian customer tidak dapat dikembalikan.',
            'invoice hilang akan dikenakan biaya cetak ulang struk Rp. 2.000 saat pengambilan.'
        ];

        foreach ($pesan as $value) {
            DB::table('setting_pesan_struk')->insert(array_merge([
                'pesan'  => $value
            ], $time));
        }
    }
}
