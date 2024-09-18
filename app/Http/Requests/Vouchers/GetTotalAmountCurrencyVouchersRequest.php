<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetTotalAmountCurrencyVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', 'max:3'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'currency.required' => 'El campo moneda es obligatorio.',
            'currency.string' => 'El campo moneda debe ser una cadena de texto.',
            'currency.max' => 'El campo moneda debe tener máximo 3 caracteres.',
        ];
    }
}
