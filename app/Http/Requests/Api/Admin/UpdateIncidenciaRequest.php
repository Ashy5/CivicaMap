<?php

namespace App\Http\Requests\Api\Admin;

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
            'id_usuario' => ['sometimes', 'required', 'integer', 'exists:usuarios,id_usuario'],
            'id_categoria' => ['sometimes', 'required', 'integer', 'exists:categorias_reporte,id_categoria'],
            'descripcion' => ['sometimes', 'required', 'string', 'min:10', 'max:2000'],
            'latitud_aprox' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitud_aprox' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'fecha_reporte' => ['sometimes', 'required', 'date'],
            'estado' => ['sometimes', 'required', 'string', 'max:30'],
            'anonimizado' => ['sometimes', 'boolean'],
        ];
    }
}
