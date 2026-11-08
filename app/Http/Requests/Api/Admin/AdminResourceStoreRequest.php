<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminResourceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['required', 'string', 'max:80'],
            'costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'estado' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
