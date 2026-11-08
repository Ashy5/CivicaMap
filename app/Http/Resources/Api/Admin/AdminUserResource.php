<?php

namespace App\Http\Resources\Api\Admin;

use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Administrador) {
            return [
                'id' => $this->id,
                'tipo_usuario' => 'administrador',
                'nombre' => $this->nombre_completo,
                'username' => $this->username,
                'nivel' => $this->nivel,
                'estado' => $this->estado,
                'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn ($role): array => [
                    'id_rol' => $role->id_rol,
                    'nombre' => $role->nombre,
                    'descripcion' => $role->descripcion,
                ])->values()),
            ];
        }

        return [
            'id' => $this->id_usuario,
            'tipo_usuario' => 'cliente',
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'fecha_registro' => $this->fecha_registro,
            'estado' => $this->estado,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn ($role): array => [
                'id_rol' => $role->id_rol,
                'nombre' => $role->nombre,
                'descripcion' => $role->descripcion,
            ])->values()),
        ];
    }
}
