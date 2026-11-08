<?php

namespace App\Policies;

use App\Models\Administrador;
use App\Models\Servicio;
use App\Models\Usuario;

class ServicioPolicy
{
    public function viewAny(Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function view(Usuario|Administrador $user, Servicio $servicio): bool
    {
        return $this->isAdministrator($user);
    }

    public function create(Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(Usuario|Administrador $user, Servicio $servicio): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete(Usuario|Administrador $user, Servicio $servicio): bool
    {
        return $this->isAdministrator($user);
    }

    public function restore(Usuario|Administrador $user, Servicio $servicio): bool
    {
        return $this->isAdministrator($user);
    }

    public function forceDelete(Usuario|Administrador $user, Servicio $servicio): bool
    {
        return $this->isAdministrator($user);
    }

    private function isAdministrator(Usuario|Administrador $user): bool
    {
        return $user instanceof Administrador
            && $user->estado
            && (
                $user->nivel >= 3
                || $user->hasRole(['Administrador', 'Admin', 'SuperAdmin'])
            );
    }
}
