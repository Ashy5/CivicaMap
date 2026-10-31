<?php

namespace App\Policies;

use App\Models\Administrador;
use App\Models\CategoriaReporte;
use App\Models\Usuario;

class CategoriaPolicy
{
    public function viewAny(Usuario|Administrador $user): bool
    {
        if ($user instanceof Administrador) {
            return $user->estado && (
                $user->nivel >= 1
                || $user->hasRole(['Admin', 'SuperAdmin', 'Seguridad'])
                || $user->hasPermissionTo('categorias.viewAny')
            );
        }

        return $user->estado && (
            $user->hasRole(['Seguridad', 'Admin', 'SuperAdmin'])
            || $user->hasPermissionTo('categorias.viewAny')
        );
    }

    public function view(Usuario|Administrador $user, CategoriaReporte $categoria): bool
    {
        return $this->viewAny($user);
    }
}
