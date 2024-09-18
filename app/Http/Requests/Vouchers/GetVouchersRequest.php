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

    public function messages(): array
    {
        return [
            'page.required' => 'El campo página es obligatorio.',
            'page.int' => 'El campo página debe ser un número entero.',
            'page.gt' => 'El campo página debe ser mayor a 0.',
            'paginate.required' => 'El campo paginar es obligatorio.',
            'paginate.int' => 'El campo paginar debe ser un número entero.',
            'paginate.gt' => 'El campo paginar debe ser mayor a 0.',
            'paginate.lte' => 'El campo paginar debe ser menor o igual a 100.',
            'serie.string' => 'El campo serie debe ser una cadena de texto.',
            'number.string' => 'El campo número debe ser una cadena de texto.',
            'from.date_format' => 'El campo desde debe tener el formato Y-m-d.',
            'to.date_format' => 'El campo hasta debe tener el formato Y-m-d.',
            'to.after_or_equal' => 'El campo hasta debe ser mayor o igual al campo desde.',
        ];
    }
}
