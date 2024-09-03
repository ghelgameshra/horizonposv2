<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;

class ProdukInsertRequest extends FormRequest
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
            'nama_produk'   => ['required', 'string', 'max:150'],
            'id_kategori'   => ['required', 'numeric', 'max:150'],
            'harga_beli'    => ['required', 'numeric', 'min:0'],
            'harga_jual'    => ['required', 'numeric', 'min:1'],
            'merk'          => ['nullable', 'string', 'max:150'],
            'jenis_ukuran'  => ['required', 'string', 'max:25'],
            'satuan'        => ['required', 'string', 'max:25'],
            'kode_suppllier'=> ['nullable', 'string', 'max:150'],
        ];
    }
}
