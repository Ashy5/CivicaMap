<?php

namespace App\Policies;

use App\Models\Administrador;
use App\Models\Reporte;
use App\Models\Usuario;

class IncidenciaPolicy
{
    public function viewAny(Usuario|Administrador $user): bool
    {
        return $user->estado;
    }

    public function view(Usuario|Administrador $user, Reporte $incidencia): bool
    {
        if (! $user->estado) {
            return false;
        }

        if ($user instanceof Administrador) {
            return $user->nivel >= 1
                || $user->hasRole(['Admin', 'SuperAdmin', 'Seguridad'])
                || $user->hasPermissionTo('incidencias.viewAny');
        }

        return $user->id_usuario === $incidencia->id_usuario
            || $user->hasRole(['Seguridad', 'Admin', 'SuperAdmin'])
            || $user->hasPermissionTo('incidencias.viewAny');
    }

    public function updateStatus(Usuario|Administrador $user, Reporte $incidencia): bool
    {
        if (! $user->estado) {
            return false;
        }

        if ($user instanceof Administrador) {
            return $user->nivel >= 2
                || $user->hasRole(['Admin', 'SuperAdmin'])
                || $user->hasPermissionTo('incidencias.updateStatus');
        }

        return $user->hasRole(['Seguridad'])
            || $user->hasPermissionTo('incidencias.updateStatus');
    }
}
