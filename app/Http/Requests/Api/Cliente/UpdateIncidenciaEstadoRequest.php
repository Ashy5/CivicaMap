<?php

namespace App\Http\Requests\Api\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidenciaEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', Rule::in(['Pendiente', 'En Proceso', 'Resuelto'])],
        ];
    }
}
