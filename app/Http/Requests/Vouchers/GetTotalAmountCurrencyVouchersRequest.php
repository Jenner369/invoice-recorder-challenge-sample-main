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
}
