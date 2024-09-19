<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;

class PrinterInsertRequest extends FormRequest
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
            'nama_printer'          => ['required', 'string', 'max:200'],
            'jenis_printer'         => ['required', 'string', 'max:200'],
            'protocol_printer'      => ['required', 'string', 'max:200'],
            'ip_printer'            => ['required', 'string', 'max:200'],
            'port_printer'          => ['nullable', 'string', 'max:200'],
            'username_printer'      => ['required', 'string', 'max:200'],
            'password_printer'      => ['required', 'string', 'max:200'],
            'default_printer'       => ['required', 'string', 'max:15'],
            'keterangan_printer'    => ['nullable', 'string', 'max:200'],
        ];
    }
}
