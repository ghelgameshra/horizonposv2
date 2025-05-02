<?php

namespace App\Http\Controllers\Administrasi;

use App\Exports\KaryawanExport;
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

        // Update jika nik sudah ada, insert jika belum
        $karyawan = Karyawan::updateOrCreate([
            'nik'   => $data['nik'],
            'ktp'   => $data['ktp'],
            'telepone' => $data['telepone']
        ], // Kondisi pencarian
            $data                     // Data untuk insert atau update
        );

        // Hanya generate nik dan user saat insert
        if ($karyawan->wasRecentlyCreated) {
            $this->generateNik();
            $this->createUser();
            $pesan = 'Data karyawan berhasil ditambah';
            $status = 201;
        } else {
            $pesan = 'Data karyawan berhasil diupdate';
            $status = 200;
        }

        return response()->json([
            'pesan' => $pesan
        ], $status);
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

    private function createUser(): void
    {
        $notRegisterUser = DB::table('karyawan')
        ->leftJoin('users', 'karyawan.email', '=', 'users.email')
        ->select('karyawan.email', 'karyawan.nama_lengkap AS nama', 'karyawan.nik', 'karyawan.jobdesk')
        ->whereNull('users.email')
        ->get();

        foreach ($notRegisterUser as $key => $value) {
            User::create([
                'email'     => $value->email,
                'name'      => $value->nama,
                'password'  => $value->email,
                'nik'       => $value->nik,
                'job'       => $value->jobdesk
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

    public function export()
    {
        return Excel::download(new KaryawanExport, "employees_" . now()->format('Y_m_d_h_i_s') . ".xlsx");
    }
}
