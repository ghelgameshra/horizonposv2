<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TipePotongan;
use App\Enums\TipePromo;

class PromoRequest extends FormRequest
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
            "nama_promo"                => 'required|string|min:5|max:100',
            "detail_promo"              => 'required|string|min:5|max:1000',
            "tipe_promo"                => ['required', new Enum(TipePromo::class)],
            "tipe_potongan"             => ['required', new Enum(TipePotongan::class)],
            "nilai_potongan"            => 'required|min:1',
            "nominal_min_pembelian"     => 'required|min:1',
            "nominal_maks_pembelian"    => 'required|min:1',
            "tanggal_mulai"             => 'required|date',
            "tanggal_selesai"           => 'required|date',
        ];
    }
}
