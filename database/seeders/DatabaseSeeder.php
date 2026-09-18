<?php

namespace Database\Seeders;

use App\Models\CategoriaReporte;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categorias = [
            'Seguridad',
            'Acoso',
            'Infraestructura',
            'Convivencia comunitaria',
            'Otro',
        ];

        foreach ($categorias as $categoriaNombre) {
            CategoriaReporte::firstOrCreate(
                ['nombre' => $categoriaNombre],
                [
                    'descripcion' => 'Categoría de reporte generada por el sistema.',
                    'estado' => true,
                ]
            );
        }

        $usuario = Usuario::firstOrCreate(
            ['correo' => 'test@example.com'],
            [
                'nombre' => 'Usuario de prueba',
                'password' => 'secret',
                'fecha_registro' => now(),
                'estado' => true,
            ]
        );

        $categoria = CategoriaReporte::where('nombre', 'Seguridad')->first();

        Reporte::firstOrCreate(
            ['descripcion' => 'Hay basura acumulada en la esquina principal.'],
            [
                'id_usuario' => $usuario->id_usuario,
                'id_categoria' => $categoria?->id_categoria ?? 1,
                'latitud_aprox' => 20.6597,
                'longitud_aprox' => -103.3496,
                'fecha_reporte' => now(),
                'estado' => 'Recibido',
                'anonimizado' => true,
            ]
        );
    }
}
