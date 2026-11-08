<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;

class AdminUserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_usuario' => ['required', 'string', 'in:cliente,administrador'],
            'estado' => ['sometimes', 'required', 'boolean'],
            'role_ids' => ['sometimes', 'required', 'array'],
            'role_ids.*' => ['required', 'integer', 'distinct', 'exists:roles,id_rol'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->exists('estado') && ! $this->exists('role_ids')) {
                $validator->errors()->add('estado', 'Debe indicar el estado o los roles que desea actualizar.');

                return;
            }

            if (
                $this->input('tipo_usuario') === 'administrador'
                && $this->exists('role_ids')
                && ! Rol::whereIn('id_rol', $this->input('role_ids', []))
                    ->get()
                    ->contains(fn (Rol $role): bool => in_array(
                        Str::lower($role->nombre),
                        ['administrador', 'admin', 'superadmin'],
                        true
                    ))
            ) {
                $validator->errors()->add('role_ids', 'Un administrador debe conservar un rol administrativo.');
            }
        });
    }
}
