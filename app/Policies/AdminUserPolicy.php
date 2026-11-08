<?php

namespace App\Policies;

use App\Models\Administrador;
use App\Models\Usuario;

class AdminUserPolicy
{
    public function viewAny(Administrador $administrator): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function view(Administrador $administrator, Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function create(Administrador $administrator): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function update(Administrador $administrator, Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function delete(Administrador $administrator, Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function restore(Administrador $administrator, Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($administrator);
    }

    public function forceDelete(Administrador $administrator, Usuario|Administrador $user): bool
    {
        return $this->isAdministrator($administrator);
    }

    private function isAdministrator(Administrador $administrator): bool
    {
        return $administrator->estado
            && (
                $administrator->nivel >= 3
                || $administrator->hasRole(['Administrador', 'Admin', 'SuperAdmin'])
            );
    }
}
