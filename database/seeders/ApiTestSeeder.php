<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\CategoriaReporte;
use App\Models\Reporte;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class ApiTestSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Rol::firstOrCreate(
            ['nombre' => 'SuperAdmin'],
            ['descripcion' => 'Administración completa de CivicaMap.']
        );
        $managerRole = Rol::firstOrCreate(
            ['nombre' => 'Gestor Operativo'],
            ['descripcion' => 'Gestión operativa de incidencias.']
        );
        $securityRole = Rol::firstOrCreate(
            ['nombre' => 'Seguridad'],
            ['descripcion' => 'Lectura global y seguimiento de incidencias.']
        );

        $superAdmin = Administrador::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nombre_completo' => 'Super Administrador de prueba',
                'password' => 'CivicaSuper2026!',
                'nivel' => 3,
                'estado' => true,
            ]
        );
        $manager = Administrador::updateOrCreate(
            ['username' => 'gestor'],
            [
                'nombre_completo' => 'Gestor Operativo de prueba',
                'password' => 'CivicaGestor2026!',
                'nivel' => 2,
                'estado' => true,
            ]
        );

        $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id_rol]);
        $manager->roles()->syncWithoutDetaching([$managerRole->id_rol]);

        $citizen = Usuario::updateOrCreate(
            ['correo' => 'ciudadano@civicamap.test'],
            [
                'nombre' => 'Ciudadano de prueba',
                'password' => 'CivicaCiudadano2026!',
                'fecha_registro' => now(),
                'estado' => true,
            ]
        );
        $security = Usuario::updateOrCreate(
            ['correo' => 'seguridad@civicamap.test'],
            [
                'nombre' => 'Agente de Seguridad de prueba',
                'password' => 'CivicaSeguridad2026!',
                'fecha_registro' => now(),
                'estado' => true,
            ]
        );
        $security->roles()->syncWithoutDetaching([$securityRole->id_rol]);

        $categories = collect([
            ['nombre' => 'Seguridad', 'descripcion' => 'Seguridad ciudadana y prevención.'],
            ['nombre' => 'Infraestructura', 'descripcion' => 'Calles, banquetas y espacios públicos.'],
            ['nombre' => 'Alumbrado Público', 'descripcion' => 'Luminarias y alumbrado de la ciudad.'],
        ])->mapWithKeys(function (array $category): array {
            $model = CategoriaReporte::updateOrCreate(
                ['nombre' => $category['nombre']],
                ['descripcion' => $category['descripcion'], 'estado' => true]
            );

            return [$category['nombre'] => $model];
        });

        $incidents = [
            [
                'descripcion' => 'Luminaria apagada en el parque central.',
                'id_usuario' => $citizen->id_usuario,
                'id_categoria' => $categories['Alumbrado Público']->id_categoria,
                'estado' => 'Pendiente',
                'latitud_aprox' => 20.65970,
                'longitud_aprox' => -103.34960,
            ],
            [
                'descripcion' => 'Bache profundo frente a la escuela primaria.',
                'id_usuario' => $citizen->id_usuario,
                'id_categoria' => $categories['Infraestructura']->id_categoria,
                'estado' => 'En Proceso',
                'latitud_aprox' => 20.66110,
                'longitud_aprox' => -103.35120,
            ],
            [
                'descripcion' => 'Señal de tránsito dañada en el cruce principal.',
                'id_usuario' => $security->id_usuario,
                'id_categoria' => $categories['Seguridad']->id_categoria,
                'estado' => 'Resuelto',
                'latitud_aprox' => 20.65780,
                'longitud_aprox' => -103.34790,
            ],
            [
                'descripcion' => 'Banqueta obstruida por escombro en la avenida.',
                'id_usuario' => $citizen->id_usuario,
                'id_categoria' => $categories['Infraestructura']->id_categoria,
                'estado' => 'Pendiente',
                'latitud_aprox' => 20.66300,
                'longitud_aprox' => -103.35400,
            ],
            [
                'descripcion' => 'Luminaria intermitente cerca de la parada de autobús.',
                'id_usuario' => $security->id_usuario,
                'id_categoria' => $categories['Alumbrado Público']->id_categoria,
                'estado' => 'En Proceso',
                'latitud_aprox' => 20.65590,
                'longitud_aprox' => -103.34610,
            ],
        ];

        foreach ($incidents as $incident) {
            Reporte::updateOrCreate(
                ['descripcion' => $incident['descripcion']],
                $incident + [
                    'fecha_reporte' => now(),
                    'anonimizado' => false,
                ]
            );
        }
    }
}
