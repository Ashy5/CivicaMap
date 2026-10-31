<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_usuario' => ['required', 'integer', 'exists:usuarios,id_usuario'],
            'id_categoria' => ['required', 'integer', 'exists:categorias_reporte,id_categoria'],
            'descripcion' => ['required', 'string', 'min:10', 'max:2000'],
            'latitud_aprox' => ['required', 'numeric', 'between:-90,90'],
            'longitud_aprox' => ['required', 'numeric', 'between:-180,180'],
            'fecha_reporte' => ['sometimes', 'date'],
            'estado' => ['sometimes', 'string', 'max:30'],
            'anonimizado' => ['sometimes', 'boolean'],
        ];
    }
}
