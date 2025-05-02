<?php

namespace App\Exports;

use App\Models\Administrasi\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Karyawan::all();
    }

    public function map($row): array
    {
        return [
            $row->nama_lengkap,
            $row->nik,
            $row->jabatan,
            $row->email,
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->alamat_domisili,
            $row->telepone,
            $row->jobdesk,
            $row->agama,
            $row->ktp,
            $row->status_pernikahan ? 'Y' : 'N',
            $row->pendidikan_terakhir,
            $row->tanggal_masuk,
            $row->status_karyawan ? 'AKTIF' : 'NONAKTIF',
            $row->created_at,
            $row->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIK',
            'Jabatan',
            'Email',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat Domisili',
            'Telepone',
            'Jobdesk',
            'Agama',
            'KTP',
            'Status Pernikahan',
            'Pendidikan Terakhir',
            'Tanggal Masuk',
            'Status Karyawan',
            'Dibuat Pada',
            'Diperbarui Pada',
        ];
    }
}
