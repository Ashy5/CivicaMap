<?php

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

Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
