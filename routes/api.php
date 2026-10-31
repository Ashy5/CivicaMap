<?php

use App\Http\Controllers\Api\Admin\CategoriaController as AdminCategoriaController;
use App\Http\Controllers\Api\Admin\IncidenciaController as AdminIncidenciaController;
use App\Http\Controllers\Api\Admin\RolController as AdminRolController;
use App\Http\Controllers\Api\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Cliente\CategoriaController as ClienteCategoriaController;
use App\Http\Controllers\Api\Cliente\IncidenciaController as ClienteIncidenciaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('cliente')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('api.v1.cliente.login');
        Route::post('/registro', [AuthController::class, 'register'])->name('api.v1.cliente.register');

        Route::middleware(['auth:sanctum', 'client.account'])->group(function () {
            Route::get('/perfil', [AuthController::class, 'profile'])->name('api.v1.cliente.profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.cliente.logout');
            Route::get('/categorias', [ClienteCategoriaController::class, 'index']);
            Route::patch('/incidencias/{id}/estado', [ClienteIncidenciaController::class, 'updateStatus']);
            Route::apiResource('incidencias', ClienteIncidenciaController::class)
                ->parameters(['incidencias' => 'id'])
                ->except(['create', 'edit']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin'])->middleware('throttle:5,1')->name('api.v1.admin.login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/perfil', [AuthController::class, 'profile'])->middleware('admin.role:ver-reportes');
            Route::post('/logout', [AuthController::class, 'logout'])->middleware('admin.role:ver-reportes');

            Route::get('/incidencias', [AdminIncidenciaController::class, 'index'])->middleware('admin.role:ver-reportes');
            Route::get('/incidencias/{id}', [AdminIncidenciaController::class, 'show'])->middleware('admin.role:ver-reportes');
            Route::post('/incidencias', [AdminIncidenciaController::class, 'store'])->middleware('admin.role:crear-reportes');
            Route::match(['put', 'patch'], '/incidencias/{id}', [AdminIncidenciaController::class, 'update'])->middleware('admin.role:editar-reportes');
            Route::delete('/incidencias/{id}', [AdminIncidenciaController::class, 'destroy'])->middleware('admin.role:eliminar-reportes');

            Route::apiResource('categorias', AdminCategoriaController::class)
                ->parameters(['categorias' => 'id'])
                ->middleware('admin.role:administrar-categorias')
                ->except(['create', 'edit']);

            Route::apiResource('usuarios', AdminUsuarioController::class)
                ->parameters(['usuarios' => 'id'])
                ->middleware('admin.role:administrar-usuarios')
                ->except(['create', 'edit']);

            Route::post('/usuarios/{id}/roles', [AdminUsuarioController::class, 'syncRoles'])
                ->middleware('admin.role:administrar-usuarios');
            Route::delete('/usuarios/{id}/roles/{roleId}', [AdminUsuarioController::class, 'removeRole'])
                ->middleware('admin.role:administrar-usuarios');
            Route::post('/administradores/{id}/roles', [AdminRolController::class, 'syncAdministratorRoles'])
                ->middleware('admin.role:administrar-roles');
            Route::delete('/administradores/{id}/roles/{roleId}', [AdminRolController::class, 'removeAdministratorRole'])
                ->middleware('admin.role:administrar-roles');
            Route::get('/roles', [AdminRolController::class, 'index'])
                ->middleware('admin.role:administrar-roles');
            Route::post('/roles', [AdminRolController::class, 'store'])
                ->middleware('admin.role:administrar-roles');
        });
    });
});
