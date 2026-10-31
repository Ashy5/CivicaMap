<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('categorias_reporte', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }
}
