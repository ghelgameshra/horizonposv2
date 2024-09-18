<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataTokoRequest extends FormRequest
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
            'nama_perusahaan'       => ['required', 'string', 'max:150'],
            'email'                 => ['required', 'string', 'max:50'],
            'telepone'              => ['required', 'string', 'max:15'],
            'whatsapp'              => ['required', 'string', 'max:15'],
            'instagram'             => ['required', 'string', 'max:100'],
            'facebook'              => ['required', 'string', 'max:100'],
            'tiktok'                => ['required', 'string', 'max:100'],
            'web'                   => ['required', 'string', 'max:200'],
            'alamat_lengkap'        => ['required', 'string', 'max:300'],
        ];
    }
}
