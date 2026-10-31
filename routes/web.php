<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ReporteController;
use App\Models\CategoriaReporte;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/mapa', 'mapa')->name('mapa');

Route::get('/denuncias', function () {
    $categorias = CategoriaReporte::where('estado', true)->get();

    if ($categorias->isEmpty()) {
        $categorias = collect([
            ['nombre' => 'Seguridad', 'estado' => true],
            ['nombre' => 'Acoso', 'estado' => true],
            ['nombre' => 'Infraestructura', 'estado' => true],
            ['nombre' => 'Convivencia comunitaria', 'estado' => true],
            ['nombre' => 'Otro', 'estado' => true],
        ])->map(function ($categoria) {
            return CategoriaReporte::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                ['descripcion' => 'Categoría de reporte.', 'estado' => true]
            );
        });
    }

    return view('denuncias', ['categorias' => $categorias]);
})->name('denuncias');

Route::view('/servicios', 'servicios')->name('servicios');
Route::view('/login', 'auth.login')->name('login');
Route::view('/registro', 'auth.registro')->name('registro');

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::view('/panel', 'admin.dashboard')->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::middleware('auth:admin')->group(function () {

    // Consultar reportes: todos los niveles
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('can:ver-reportes')
        ->name('reportes.index');

    // Editar reportes: niveles 2 y 3
    Route::get('/reportes/{reporte}/edit', [ReporteController::class, 'edit'])
        ->middleware('can:editar-reportes')
        ->name('reportes.edit');

    // Crear reportes: niveles 2 y 3
    Route::post('/reportes', [ReporteController::class, 'store'])
        ->middleware('can:crear-reportes')
        ->name('reportes.store');

    // Actualizar reportes: niveles 2 y 3
    Route::match(['PUT', 'PATCH'], '/reportes/{reporte}', [ReporteController::class, 'update'])
        ->middleware('can:editar-reportes')
        ->name('reportes.update');

    // Eliminar reportes: solo nivel 3
    Route::delete('/reportes/{reporte}', [ReporteController::class, 'destroy'])
        ->middleware('can:eliminar-reportes')
        ->name('reportes.destroy');
});
