<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminResourceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:120'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'tipo' => ['sometimes', 'required', 'string', 'max:80'],
            'costo' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'estado' => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isEmpty() && $this->only([
                'nombre',
                'descripcion',
                'tipo',
                'costo',
                'estado',
            ]) === []) {
                $validator->errors()->add('request', 'Debe indicar al menos un campo para actualizar.');
            }
        });
    }
}
