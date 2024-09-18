<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe ser un email válido.',
            'email.unique' => 'El email ya está en uso.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'El campo contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
