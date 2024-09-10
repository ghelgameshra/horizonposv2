<?php

namespace App\Imports;

use App\Models\Administrasi\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class KaryawanImport implements ToModel, WithUpserts, WithHeadingRow, WithBatchInserts
{
    public function uniqueBy()
    {
        return 'nik'; // Kolom unik yang digunakan untuk identifikasi
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Karyawan([
            'nama_lengkap'          => $row['nama_lengkap'],
            'jabatan'               => $row['jabatan'],
            'tempat_lahir'          => $row['tempat_lahir'],
            'tanggal_lahir'         => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']),
            'alamat_domisili'       => $row['alamat_domisili'],
            'telepone'              => $row['telepone'],
            'jobdesk'               => $row['jobdesk'],
            'agama'                 => $row['agama'],
            'ktp'                   => $row['ktp'],
            'status_pernikahan'     => $row['status_pernikahan'],
            'pendidikan_terakhir'   => $row['pendidikan_terakhir'],
            'tanggal_masuk'         => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_masuk']),
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 2000;
    }
}
