<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0', 'lte:100'], // Max 100 para evitar problemas de performance
            'serie' => ['nullable', 'string'],
            'number' => ['nullable', 'string'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    public function getFilters(): array
    {
        return [
            'serie' => $this->input('serie'),
            'number' => $this->input('number'),
            'from' => ($this->input('from')) ? Carbon::parse($this->input('from'))->startOfDay() : null,
            'to' => ($this->input('to')) ? Carbon::parse($this->input('to'))->endOfDay() : null,
        ];
    }
}
