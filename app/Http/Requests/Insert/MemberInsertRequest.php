<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;

class MemberInsertRequest extends FormRequest
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
            'nama_lengkap'      => ['required', 'string', 'max:200', 'unique:member,nama_lengkap'],
            'alamat_lengkap'    => ['required', 'string', 'max:300'],
            'telepone'          => ['required', 'string', 'max:14', 'unique:member,telepone'],
            'email'             => ['nullable', 'string', 'max:150', 'unique:member,email'],
        ];
    }
}
