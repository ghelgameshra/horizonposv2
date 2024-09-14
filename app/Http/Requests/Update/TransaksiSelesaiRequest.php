<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiSelesaiRequest extends FormRequest
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
            'nama_customer'     => ['required', 'string', 'max:50'],
            'nomor_telepone'    => ['required', 'string', 'max:15'],
            'terima'            => ['required', 'numeric'],
            'tipe_bayar'        => ['required', 'string', 'max:15']
        ];
    }
}
