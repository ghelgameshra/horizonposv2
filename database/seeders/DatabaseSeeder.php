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
            AgamaSeeder::class,
            AntrianSeeder::class,
            ConstSeeder::class,
            JabatanSeeder::class,
            JenisPengeluaranSeeder::class,
            UkuranSatuanSeeder::class,
            PendidikanSeeder::class,
            PromoSeeder::class,
            SettingStrukSeeder::class,
            StatusOrderSeeder::class,
            TokoSeeder::class,
            UserApiSeeder::class,
            UserSeeder::class,
        ]);
    }
}
