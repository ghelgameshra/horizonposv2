<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua permission sebelumnya
        Permission::query()->delete();

        // Daftar izin untuk view
        $view = [
            'dashboard', 'kasir', 'master', 'transaksi', 'pelunasan', 'laporan',
            'pengeluaran', 'tutup harian', 'pengaturan', 'pesanan', 'produk',
            'member', 'promosi', 'karyawan', 'pelunasan', 'reprint', 'ambil pesanan',
            'work order', 'lisensi', 'toko', 'printer', 'server', 'user'
        ];

        // Daftar aksi CRUD
        $permissions = ["create", "read", "update", "delete"];

        // Menu yang digunakan untuk aksi CRUD
        $menu = [
            "dashboard", "master", "produk", "member", "promosi", "karyawan",
            "transaksi", "pelunasan", "reprint transaksi", "pengambilan",
            "work order", "pengeluaran", "tutup harian", "pengaturan", "toko",
            "printer", "lisensi", "user", "roles", "permissions"
        ];

        // Buat izin untuk view
        foreach ($view as $value) {
            Permission::firstOrCreate([
                "name" => "show " . $value
            ]);
        }

        // Buat izin untuk CRUD
        foreach ($permissions as $action) {
            foreach ($menu as $item) {
                $permissionName = "$action $item";
                Permission::firstOrCreate([
                    "name" => $permissionName
                ]);
            }
        }
    }
}
