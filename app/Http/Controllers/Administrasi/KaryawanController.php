<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\KaryawanInsertRequest;
use App\Imports\KaryawanImport;
use App\Models\Administrasi\Karyawan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public function get(): JsonResponse
    {
        $data = DB::table('karyawan')->select(['nama_lengkap', 'nik', 'telepone', 'jobdesk', 'jabatan', 'status_karyawan'])->get();
        return response()->json([
            'data'  => $data,
            'pesan' => 'berhasil ambil data karyawan'
        ], 200);
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'file_karyawan' => ['required', 'file', 'mimes:csv,xlsx', 'max:10000']
        ]);

        /* import from excel */
        Excel::import(new KaryawanImport, $request->file('file_karyawan'));

        /* generate NIK by tanggal_lahir */
        $this->generateNik();

        /* create user */
        $this->createUser();

        return response()->json([
            'pesan' => "berhasil insert data karyawan"
        ], 201);
    }

    public function delete(Request $request): JsonResponse
    {
        $data = Karyawan::where('nik', $request->nik)->first();
        $data->delete();
        return response()->json([
            'pesan' => "Data karyawan dengan NIK $request->nik berhasil dihapus"
        ], 200);
    }

    public function insert(KaryawanInsertRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['nama_lengkap'] = strtoupper($data['nama_lengkap']);
        $data['alamat_domisili'] = ucwords($data['alamat_domisili']);

        Karyawan::create($data);
        $this->generateNik();
        $this->createUser();

        return response()->json([
            'pesan' => 'Data karyawan berhasil ditambah'
        ], 201);
    }

    private function generateNik(): void
    {
        $karyawan = Karyawan::where('nik', NULL)->get();
        foreach ($karyawan as $value) {
            $tanggalLahir = Carbon::parse($value->tanggal_lahir);
            $tanggalMasuk = Carbon::parse($value->tanggal_masuk);

            $value->update([
                'nik'   =>  $tanggalMasuk->format('Y') . $tanggalLahir->format('ymd')
            ]);
        }
    }

    private function createUser(): void{
        $notRegisterUser = DB::table('karyawan')
        ->leftJoin('users', 'karyawan.email', '=', 'users.email')
        ->select('karyawan.email', 'karyawan.nama_lengkap AS nama')
        ->whereNull('users.email')
        ->get();

        foreach ($notRegisterUser as $key => $value) {
            User::create([
                'email'     => $value->email,
                'name'      => $value->nama,
                'password'  => $value->email
            ]);
        }
    }

    public function detail($nik): JsonResponse
    {
        $data = Karyawan::where('nik', $nik)->first();

        return response()->json([
            'pesan' => "berhasil ambil data detail karyawan nik $nik",
            'data'  => $data
        ], 200);
    }
}
