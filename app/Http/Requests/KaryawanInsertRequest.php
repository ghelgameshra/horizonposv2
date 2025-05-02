<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KaryawanInsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik'                   => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'nama_lengkap'          => ['required', 'string', 'max:150'],
            'jabatan'               => ['required', 'string', 'max:150'],
            'tempat_lahir'          => ['required', 'string', 'max:150'],
            'email'                 => ['required', 'string', 'max:150'],
            'tanggal_lahir'         => ['required', 'date'],
            'alamat_domisili'       => ['required', 'string', 'max:300'],
            'telepone'              => ['required', 'string', 'max:14'],
            'jobdesk'               => ['required', 'string', 'max:100'],
            'agama'                 => ['required', 'string', 'max:50'],
            'ktp'                   => ['required', 'string', 'max:18'],
            'status_pernikahan'     => ['required', 'string', 'max:10'],
            'pendidikan_terakhir'   => ['required', 'string', 'max:150'],
            'tanggal_masuk'         => ['required', 'date'],
        ];
    }
}
