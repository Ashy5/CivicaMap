<?php

namespace App\Providers;

use App\Models\Administrador;
use App\Models\CategoriaReporte;
use App\Models\Reporte;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Policies\AdminUserPolicy;
use App\Policies\CategoriaPolicy;
use App\Policies\IncidenciaPolicy;
use App\Policies\ServicioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Reporte::class, IncidenciaPolicy::class);
        Gate::policy(CategoriaReporte::class, CategoriaPolicy::class);
        Gate::policy(Servicio::class, ServicioPolicy::class);
        Gate::policy(Usuario::class, AdminUserPolicy::class);
        Gate::policy(Administrador::class, AdminUserPolicy::class);

        // Consultar reportes: niveles 1, 2 y 3
        Gate::define('ver-reportes', function ($user) {
            return $user->nivel >= 1
                || $user->hasRole(['Administrador', 'Admin', 'SuperAdmin'])
                || $user->hasPermissionTo('ver-reportes');
        });

        // Crear reportes: niveles 2 y 3
        Gate::define('crear-reportes', function ($user) {
            return $user->nivel >= 2
                || $user->hasRole(['Admin', 'SuperAdmin'])
                || $user->hasPermissionTo('crear-reportes');
        });

        // Editar reportes: niveles 2 y 3
        Gate::define('editar-reportes', function ($user) {
            return $user->nivel >= 2
                || $user->hasRole(['Admin', 'SuperAdmin'])
                || $user->hasPermissionTo('editar-reportes');
        });

        // Eliminar reportes: solo nivel 3
        Gate::define('eliminar-reportes', function ($user) {
            return $user->nivel === 3
                || $user->hasRole(['Admin', 'SuperAdmin'])
                || $user->hasPermissionTo('eliminar-reportes');
        });

        // Administrar cuentas: solo nivel 3
        Gate::define('administrar-administradores', function ($user) {
            return $this->canManageRoles($user);
        });

        Gate::define('administrar-categorias', function ($user) {
            return $user->nivel === 3;
        });

        Gate::define('administrar-usuarios', function ($user) {
            return $this->canManageRoles($user);
        });

        Gate::define('administrar-roles', function ($user) {
            return $this->canManageRoles($user);
        });
    }

    private function canManageRoles(mixed $user): bool
    {
        return $user instanceof Administrador && $user->estado && (
            $user->nivel === 3
            || $user->hasRole(['Administrador', 'Admin', 'SuperAdmin'])
            || $user->hasPermissionTo('administrar-roles')
        );
    }
}
