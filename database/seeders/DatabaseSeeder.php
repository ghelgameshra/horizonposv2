<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UkuranSatuanSeeder::class,
            ConstSeeder::class,
            PromoSeeder::class,
            PendidikanSeeder::class,
            AgamaSeeder::class,
            JabatanSeeder::class,
            KaryawanSeeder::class,
            StatusOrderSeeder::class,
            SettingStrukSeeder::class,
            TokoSeeder::class,
            AntrianSeeder::class,
            JenisPengeluaranSeeder::class,
            UserApiSeeder::class
        ]);
    }
}
