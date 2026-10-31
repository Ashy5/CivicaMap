<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\CategoriaReporte;
use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Rol;
use App\Models\Usuario;
use Database\Seeders\ApiTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_level_three_administrator_can_create_a_role_and_assign_it_to_a_citizen(): void
    {
        $administrator = $this->createAdministrator();
        $citizen = $this->createCitizen('asignado@example.com');
        $token = $administrator->createToken('test')->plainTextToken;

        $roleResponse = $this->withToken($token)->postJson('/api/v1/admin/roles', [
            'nombre' => 'Gestor Operativo',
            'descripcion' => 'Seguimiento de incidencias',
            'permissions' => ['incidencias.viewAny', 'categorias.viewAny'],
        ]);

        $roleResponse->assertCreated()
            ->assertJsonPath('data.nombre', 'Gestor Operativo')
            ->assertJsonCount(2, 'data.permissions');

        $roleId = $roleResponse->json('data.id_rol');

        $this->withToken($token)
            ->postJson('/api/v1/admin/usuarios/'.$citizen->id_usuario.'/roles', [
                'role_ids' => [$roleId],
            ])
            ->assertOk()
            ->assertJsonPath('data.roles.0.id_rol', $roleId);

        $this->assertDatabaseHas('usuario_rol', [
            'id_usuario' => $citizen->id_usuario,
            'id_rol' => $roleId,
        ]);

        $this->assertTrue($citizen->fresh()->hasPermissionTo('categorias.viewAny'));

        $this->withToken($token)
            ->deleteJson('/api/v1/admin/usuarios/'.$citizen->id_usuario.'/roles/'.$roleId)
            ->assertOk();

        $this->assertDatabaseMissing('usuario_rol', [
            'id_usuario' => $citizen->id_usuario,
            'id_rol' => $roleId,
        ]);
    }

    public function test_security_role_can_read_the_full_incident_map_and_category_catalog(): void
    {
        $security = $this->createCitizen('seguridad@example.com');
        $other = $this->createCitizen('otro@example.com');
        $role = Rol::create(['nombre' => 'Seguridad', 'descripcion' => 'Lectura global de incidencias']);
        $security->roles()->attach($role->id_rol);
        $category = $this->createCategory('Infraestructura');
        $this->createIncident($security, $category);
        $this->createIncident($other, $category);
        $token = $security->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/cliente/categorias')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withToken($token)
            ->getJson('/api/v1/cliente/incidencias')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $otherIncident = Reporte::where('id_usuario', $other->id_usuario)->firstOrFail();
        $this->withToken($token)
            ->getJson('/api/v1/cliente/incidencias/'.$otherIncident->id_reporte)
            ->assertOk()
            ->assertJsonPath('data.id_reporte', $otherIncident->id_reporte);
    }

    public function test_security_role_can_update_incident_status(): void
    {
        $security = $this->createCitizen('seguimiento@example.com');
        $citizen = $this->createCitizen('sin-seguimiento@example.com');
        $role = Rol::create(['nombre' => 'Seguridad']);
        $security->roles()->attach($role->id_rol);
        $category = $this->createCategory('Seguimiento');
        $incident = $this->createIncident($citizen, $category);
        $securityToken = $security->createToken('test')->plainTextToken;

        $this->withToken($securityToken)
            ->patchJson('/api/v1/cliente/incidencias/'.$incident->id_reporte.'/estado', [
                'estado' => 'En Proceso',
            ])
            ->assertOk()
            ->assertJsonPath('data.estado', 'En Proceso');
    }

    public function test_regular_citizen_cannot_update_incident_status(): void
    {
        $citizen = $this->createCitizen('sin-seguimiento@example.com');
        $category = $this->createCategory('Seguimiento');
        $incident = $this->createIncident($citizen, $category);
        $citizenToken = $citizen->createToken('test')->plainTextToken;

        $this->withToken($citizenToken)
            ->patchJson('/api/v1/cliente/incidencias/'.$incident->id_reporte.'/estado', [
                'estado' => 'Resuelto',
            ])
            ->assertForbidden();
    }

    public function test_api_test_seeder_creates_the_demo_accounts_categories_and_incidents(): void
    {
        $this->seed(ApiTestSeeder::class);

        $this->assertDatabaseHas('administradores', ['username' => 'superadmin', 'nivel' => 3]);
        $this->assertDatabaseHas('administradores', ['username' => 'gestor', 'nivel' => 2]);
        $this->assertDatabaseHas('usuarios', ['correo' => 'ciudadano@civicamap.test']);
        $this->assertDatabaseHas('usuarios', ['correo' => 'seguridad@civicamap.test']);
        $this->assertDatabaseCount('categorias_reporte', 3);
        $this->assertDatabaseCount('reportes', 5);

        $this->postJson('/api/v1/cliente/login', [
            'correo' => 'ciudadano@civicamap.test',
            'password' => 'CivicaCiudadano2026!',
        ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->postJson('/api/v1/admin/login', [
            'username' => 'superadmin',
            'password' => 'CivicaSuper2026!',
        ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->seed(ApiTestSeeder::class);

        $this->assertDatabaseCount('categorias_reporte', 3);
        $this->assertDatabaseCount('reportes', 5);
    }

    public function test_citizen_without_security_role_cannot_read_the_category_catalog_or_other_incidents(): void
    {
        $citizen = $this->createCitizen('normal@example.com');
        $other = $this->createCitizen('ajeno@example.com');
        $category = $this->createCategory('Seguridad');
        $otherIncident = $this->createIncident($other, $category);
        $token = $citizen->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/cliente/categorias')
            ->assertForbidden();

        $this->withToken($token)
            ->getJson('/api/v1/cliente/incidencias/'.$otherIncident->id_reporte)
            ->assertNotFound();
    }

    public function test_dynamic_category_permission_grants_catalog_access(): void
    {
        $citizen = $this->createCitizen('permisos@example.com');
        $role = Rol::create(['nombre' => 'Gestor de catálogo']);
        $permission = Permission::create(['name' => 'categorias.viewAny']);
        $role->permissions()->attach($permission->id_permission);
        $citizen->roles()->attach($role->id_rol);
        $token = $citizen->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/cliente/categorias')
            ->assertOk();
    }

    public function test_non_admin_cannot_create_roles(): void
    {
        $administrator = $this->createAdministrator(level: 1);
        $token = $administrator->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/admin/roles', ['nombre' => 'Inspector'])
            ->assertForbidden();
    }

    public function test_dynamic_admin_role_can_manage_roles(): void
    {
        $superAdministrator = $this->createAdministrator(level: 3);
        $administrator = $this->createAdministrator(level: 1);
        $role = Rol::create(['nombre' => 'Admin']);
        $superToken = $superAdministrator->createToken('test')->plainTextToken;

        $this->withToken($superToken)
            ->postJson('/api/v1/admin/administradores/'.$administrator->id.'/roles', [
                'role_ids' => [$role->id_rol],
            ])
            ->assertOk();

        $token = $administrator->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/admin/roles', ['nombre' => 'Inspector'])
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Inspector');
    }

    private function createCitizen(string $email): Usuario
    {
        return Usuario::create([
            'nombre' => 'Ciudadano',
            'correo' => $email,
            'password' => 'password-segura',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
    }

    private function createAdministrator(int $level = 3): Administrador
    {
        return Administrador::create([
            'nombre_completo' => 'Administrador',
            'username' => 'admin-role-'.$level,
            'password' => 'password-segura',
            'estado' => true,
            'nivel' => $level,
        ]);
    }

    private function createCategory(string $name): CategoriaReporte
    {
        return CategoriaReporte::create([
            'nombre' => $name,
            'descripcion' => 'Categoría de prueba',
            'estado' => true,
        ]);
    }

    private function createIncident(Usuario $usuario, CategoriaReporte $categoria): Reporte
    {
        return Reporte::create([
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Una incidencia de prueba suficientemente descriptiva.',
            'latitud_aprox' => 20.6597,
            'longitud_aprox' => -103.3496,
            'fecha_reporte' => now(),
            'estado' => 'Recibido',
            'anonimizado' => false,
        ]);
    }
}
