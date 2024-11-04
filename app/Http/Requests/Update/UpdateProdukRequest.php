<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
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
            "nama_produk"   => ['required', 'string', 'max:150'],
            "id_kategori"   => ['required', 'string', 'numeric'],
            "harga_beli"    => ['required', 'string', 'numeric'],
            "harga_jual"    => ['required', 'string', 'numeric'],
            "jenis_ukuran"  => ['required', 'string'],
            "satuan"        => ['required', 'string'],
            "merk"          => ['nullable', 'string', 'max:150'],
            "kode_supplier" => ['nullable', 'string', 'max:4'],
        ];
    }
}
