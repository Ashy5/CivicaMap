<?php

namespace App\Http\Requests\Api\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_categoria' => ['sometimes', 'required', 'integer', 'exists:categorias_reporte,id_categoria'],
            'descripcion' => ['sometimes', 'required', 'string', 'min:10', 'max:2000'],
            'latitud_aprox' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitud_aprox' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'anonimizado' => ['sometimes', 'boolean'],
        ];
    }
}
