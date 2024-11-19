<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;

class RekeningInsertRequest extends FormRequest
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
            'nama_bank'         => ['required', 'max:50', 'string', 'unique:ref_rekening,nama_bank'],
            'nomor_rekening'    => ['required', 'max:100', 'string', 'unique:ref_rekening,nomor_rekening'],
            'nama_pemilik'      => ['required', 'max:150', 'string'],
            'default'           => ['required', 'boolean'],
        ];
    }
}
