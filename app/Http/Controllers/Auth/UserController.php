<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function get(): JsonResponse
    {
        // Ambil data user dengan roles
        $user = User::with('roles')->get();

        // Ambil data roles dengan jumlah users dan permissions langsung dari query
        $roles = Role::withCount(['users', 'permissions'])->get();

        // Format data roles sesuai kebutuhan frontend
        $rolesWithUserCount = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'usersInRole' => $role->users_count, // Menggunakan hasil withCount
                'permissionsInRole' => $role->permissions_count // Menggunakan hasil withCount
            ];
        });

        // Ambil semua permissions
        $permissions = Permission::all();

        // Return data JSON
        return response()->json([
            'pesan' => "berhasil ambil data user",
            'data'  => [
                'user'  => $user,
                'roles' => $rolesWithUserCount,
                'permissions' => $permissions
            ]
        ]);
    }

    public function changeTable(String $id, Request $request): JsonResponse
    {
        // Validasi data
        $data = $request->validate([
            'tableNumber' => 'numeric|required'
        ]);

        // Cek apakah nomor meja sudah digunakan
        $usedTable = User::where('nomor_meja', $data['tableNumber'])->exists();
        if ($usedTable) {
            return response()->json([
                'message' => "Nomor meja {$data['tableNumber']} sudah digunakan"
            ], 422);
        }

        // Temukan user berdasarkan ID
        $user = User::findOrFail($id);

        // Update nomor meja
        $user->update([
            'nomor_meja' => $data['tableNumber']
        ]);

        return response()->json([
            'message' => "Berhasil ubah meja $user->name menjadi nomor {$data['tableNumber']}",
        ]);
    }

    public function rolePermission(String $roleId): JsonResponse
    {
        $role = Role::with('permissions')->where('id', $roleId)->first();
        return response()->json([
            'message'   => "Berhasil ambil data permission role $role->name",
            'data'      => [
                'role'  => $role
            ]
        ]);
    }

    public function updatePermissions(String $roleId, Request $request): JsonResponse
    {
        $role = Role::where('id', $roleId)->first();

        $data = $request->validate([
            'permissions' => 'array|required'
        ]);

        $role->syncPermissions($data['permissions']);

        return response()->json([
            'message'   => "Berhasil ubah data permission role $role->name",
        ]);
    }

    public function updateUserRole(String $userId, Request $request): JsonResponse
    {
        $data = $request->validate([
            'role' => 'string|required'
        ]);

        $user = User::where('id', $userId)->first();
        if($user->id === Auth::user()->id){
            throw new HttpResponseException(response([
                'message'   => 'User Aktif Tidak Bisa Diubah Role'
            ], 400) );
        }

        $user->syncRoles($data['role']);

        return response()->json([
            'message'   => "Berhasil ubah role user $user->name menjadi $data[role]",
            $user
        ]);
    }
}
