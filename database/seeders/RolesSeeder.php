<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["admin","cashier","intern","it","manager","operator","owner","supervisor"];

        DB::table('roles')->delete();
        foreach ($roles as $key => $value) {
            Role::create([
                "name"  => $value
            ]);
        }

        $roles = Role::whereIn('name', ['it', 'owner', 'manager', "admin"])->get();
        foreach ($roles as $role) {
            $role->syncPermissions(Permission::all());
        }

        $user = User::where('email', "administrator@email.com")->first();
        $user->syncRoles("admin");

        $user = User::where('email', "ghelgameshra3347@gmail.com")->first();
        $user->syncRoles('it');
    }
}
