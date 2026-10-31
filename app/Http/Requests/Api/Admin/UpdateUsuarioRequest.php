<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('id');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'correo' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('usuarios', 'correo')->ignore($usuario, 'id_usuario')],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }
}
