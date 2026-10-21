<?php

namespace App\Providers;

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
        // Consultar reportes: niveles 1, 2 y 3
        Gate::define('ver-reportes', function ($user) {
            return in_array($user->nivel, [1, 2, 3], true);
        });

        // Crear reportes: niveles 2 y 3
        Gate::define('crear-reportes', function ($user) {
            return in_array($user->nivel, [2, 3], true);
        });

        // Editar reportes: niveles 2 y 3
        Gate::define('editar-reportes', function ($user) {
            return in_array($user->nivel, [2, 3], true);
        });

        // Eliminar reportes: solo nivel 3
        Gate::define('eliminar-reportes', function ($user) {
            return $user->nivel === 3;
        });

        // Administrar cuentas: solo nivel 3
        Gate::define('administrar-administradores', function ($user) {
            return $user->nivel === 3;
        });
    }
}
