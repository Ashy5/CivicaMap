<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_requires_an_administrative_role_and_issues_an_admin_token(): void
    {
        $administrator = $this->createAdministrator();

        $this->postJson('/api/v1/admin/login', [
            'username' => $administrator->username,
            'password' => 'password-segura',
        ])->assertForbidden();

        $role = Rol::create(['nombre' => 'Administrador']);
        $administrator->roles()->attach($role->id_rol);

        $response = $this->postJson('/api/v1/admin/login', [
            'username' => $administrator->username,
            'password' => 'password-segura',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $administrator->id,
            'name' => 'admin-token',
        ]);
    }

    public function test_administrator_can_manage_resources_and_listing_includes_inactive_services(): void
    {
        $administrator = $this->createAdministrator();
        Servicio::create([
            'nombre' => 'Servicio deshabilitado',
            'descripcion' => 'No disponible actualmente.',
            'tipo' => 'mantenimiento',
            'costo' => 15.50,
            'estado' => false,
        ]);

        $token = $administrator->createToken('test')->plainTextToken;
        $this->withToken($token)
            ->getJson('/api/v1/admin/recursos')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.estado', false);

        $created = $this->withToken($token)
            ->postJson('/api/v1/admin/recursos', [
                'nombre' => 'Servicio nuevo',
                'descripcion' => 'Descripción válida.',
                'tipo' => 'limpieza',
                'costo' => 25.75,
            ])
            ->assertCreated()
            ->assertJsonPath('data.estado', true);

        $id = $created->json('data.id');
        $this->withToken($token)
            ->patchJson('/api/v1/admin/recursos/'.$id, ['costo' => 30])
            ->assertOk()
            ->assertJsonPath('data.costo', '30.00');

        $this->withToken($token)
            ->patchJson('/api/v1/admin/recursos/'.$id, ['nombre' => null])
            ->assertUnprocessable();

        $this->withToken($token)
            ->patchJson('/api/v1/admin/recursos/'.$id, [])
            ->assertUnprocessable();

        $this->withToken($token)
            ->deleteJson('/api/v1/admin/recursos/'.$id)
            ->assertOk()
            ->assertJsonPath('data.estado', false);

        $this->withToken($token)
            ->postJson('/api/v1/admin/recursos', [])
            ->assertUnprocessable();
    }

    public function test_admin_logout_revokes_the_current_access_token(): void
    {
        $administrator = $this->createAdministrator();
        $token = $administrator->createToken('admin-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/admin/logout')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sesion_cerrada', true);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => Administrador::class,
            'tokenable_id' => $administrator->id,
            'name' => 'admin-token',
        ]);
    }

    public function test_non_administrator_cannot_create_or_list_admin_resources(): void
    {
        $administrator = $this->createAdministrator(level: 1);
        $token = $administrator->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/admin/recursos')
            ->assertForbidden();

        $this->withToken($token)
            ->postJson('/api/v1/admin/recursos', [
                'nombre' => 'No autorizado',
                'tipo' => 'limpieza',
                'costo' => 10,
            ])
            ->assertForbidden();
    }

    public function test_customer_token_cannot_delete_an_admin_resource(): void
    {
        $customer = Usuario::create([
            'nombre' => 'Cliente de prueba',
            'correo' => 'cliente-forbidden@example.com',
            'password' => 'password-segura',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        $service = Servicio::create([
            'nombre' => 'Servicio protegido',
            'descripcion' => 'No debe ser eliminado por un cliente.',
            'tipo' => 'limpieza',
            'costo' => 10,
            'estado' => true,
        ]);

        $this->withToken($customer->createToken('test')->plainTextToken)
            ->deleteJson('/api/v1/admin/recursos/'.$service->id_servicio)
            ->assertForbidden();

        $this->assertDatabaseHas('servicios', [
            'id_servicio' => $service->id_servicio,
            'estado' => true,
        ]);
    }

    public function test_user_listing_includes_customers_and_administrators_and_status_can_be_changed(): void
    {
        $administrator = $this->createAdministrator();
        $customer = Usuario::create([
            'nombre' => 'Cliente de prueba',
            'correo' => 'cliente@example.com',
            'password' => 'password-segura',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        $role = Rol::create(['nombre' => 'Ciudadano']);
        $customerToken = $customer->createToken('test')->plainTextToken;
        $adminToken = $administrator->createToken('test')->plainTextToken;

        $this->withToken($adminToken)
            ->getJson('/api/v1/admin/usuarios')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['tipo_usuario' => 'cliente'])
            ->assertJsonFragment(['tipo_usuario' => 'administrador']);

        $this->withToken($adminToken)
            ->patchJson('/api/v1/admin/usuarios/'.$customer->id_usuario.'/estado', [
                'tipo_usuario' => 'cliente',
                'estado' => false,
                'role_ids' => [$role->id_rol],
            ])
            ->assertOk()
            ->assertJsonPath('data.estado', false)
            ->assertJsonPath('data.roles.0.id_rol', $role->id_rol);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => Usuario::class,
            'tokenable_id' => $customer->id_usuario,
            'name' => 'test',
        ]);

        $this->withToken($adminToken)
            ->patchJson('/api/v1/admin/usuarios/'.$customer->id_usuario.'/estado', [
                'tipo_usuario' => 'cliente',
            ])
            ->assertUnprocessable();
    }

    private function createAdministrator(int $level = 3): Administrador
    {
        return Administrador::create([
            'nombre_completo' => 'Administrador de prueba',
            'username' => 'admin-'.$level.'-'.uniqid(),
            'password' => 'password-segura',
            'estado' => true,
            'nivel' => $level,
        ]);
    }
}
