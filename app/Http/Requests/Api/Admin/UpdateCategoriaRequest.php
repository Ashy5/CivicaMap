<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoria = $this->route('id');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:80', Rule::unique('categorias_reporte', 'nombre')->ignore($categoria, 'id_categoria')],
            'descripcion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }
}
