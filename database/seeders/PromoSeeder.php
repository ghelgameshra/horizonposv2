<?php

namespace Database\Seeders;

use App\Models\Produk\Promo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promo')->delete();
        Promo::create([
            'kode_promo'            => 'MBRTKP24',
            'item_syarat'           => '*',
            'promo_member'          => true,
            'minimal_belanja'       => 50000,
            'maksimal_belanja'      => 10000000,
            'potongan_persentase'   => 5,
            'potongan_minimal'      => 2500,
            'potongan_maksimal'     => 1000000,
            'jenis_promo'           => 'POTONGAN PERSENTASE',
            'mekanisme_promo'       => 'POTONGAN UNTUK MEMBER AKTIF, PEMBELIAN LEBIH DARI RP. 50.000 DAPAT DISKON 5% DARI   TOTAL PEMBELIAN',
            'periode_awal'          => now(),
            'periode_akhir'         => '2030-12-12',
            'addid'                 => 'initial@setting',
        ]);
    }
}
