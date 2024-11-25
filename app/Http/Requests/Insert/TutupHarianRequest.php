<?php

namespace App\Http\Requests\Insert;

use Illuminate\Foundation\Http\FormRequest;

class TutupHarianRequest extends FormRequest
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
            'rp100000'  => ['required', 'numeric'],
            'rp75000'   => ['required', 'numeric'],
            'rp50000'   => ['required', 'numeric'],
            'rp20000'   => ['required', 'numeric'],
            'rp10000'   => ['required', 'numeric'],
            'rp5000'    => ['required', 'numeric'],
            'rp1000'    => ['required', 'numeric'],
            'rp2000'    => ['required', 'numeric'],
            'rp500'     => ['required', 'numeric'],
            'rp200'     => ['required', 'numeric'],
            'rp100'     => ['required', 'numeric'],
            'password'  => ['required', 'string', 'max:150']
        ];
    }
}
