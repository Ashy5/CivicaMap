<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\CategoriaReporte;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_register_and_receive_a_bearer_token(): void
    {
        $response = $this->postJson('/api/v1/cliente/registro', [
            'nombre' => 'Ciudadana de prueba',
            'correo' => 'ciudadana@example.com',
            'password' => 'password-segura',
            'password_confirmation' => 'password-segura',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonStructure(['success', 'message', 'data' => ['token', 'token_type', 'user']]);

        $this->assertDatabaseHas('usuarios', ['correo' => 'ciudadana@example.com']);

        $this->withToken($response->json('data.token'))
            ->getJson('/api/v1/cliente/perfil')
            ->assertOk()
            ->assertJsonPath('data.correo', 'ciudadana@example.com');
    }

    public function test_citizen_can_create_an_incident_without_setting_an_owner(): void
    {
        $citizen = $this->createCitizen('crear@example.com');
        $categoria = CategoriaReporte::create([
            'nombre' => 'Infraestructura',
            'descripcion' => 'Categoría de prueba',
            'estado' => true,
        ]);
        $token = $citizen->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/cliente/incidencias', [
                'id_usuario' => 999,
                'id_categoria' => $categoria->id_categoria,
                'descripcion' => 'Hay un bache grande en la calle principal.',
                'latitud_aprox' => 20.6597,
                'longitud_aprox' => -103.3496,
            ])
            ->assertCreated()
            ->assertJsonPath('data.id_usuario', $citizen->id_usuario)
            ->assertJsonPath('data.estado', 'Recibido');
    }

    public function test_citizen_can_only_access_their_own_incidents(): void
    {
        $owner = $this->createCitizen('propietario@example.com');
        $other = $this->createCitizen('otro@example.com');
        $incident = $this->createIncident($owner);
        $token = $owner->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/cliente/incidencias/'.$incident->id_reporte)
            ->assertOk()
            ->assertJsonPath('data.id_reporte', $incident->id_reporte);

        $otherIncident = $this->createIncident($other);
        $this->withToken($token)
            ->getJson('/api/v1/cliente/incidencias/'.$otherIncident->id_reporte)
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_private_api_rejects_missing_tokens_with_uniform_json(): void
    {
        $this->getJson('/api/v1/cliente/perfil')
            ->assertUnauthorized()
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJsonPath('success', false);
    }

    public function test_admin_level_controls_incident_management_routes(): void
    {
        $administrator = $this->createAdministrator(level: 1);
        $token = $administrator->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/admin/incidencias')
            ->assertOk();

        $this->withToken($token)
            ->postJson('/api/v1/admin/incidencias', [])
            ->assertForbidden()
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function test_invalid_incident_payload_uses_uniform_validation_response(): void
    {
        $citizen = $this->createCitizen('validacion@example.com');
        $token = $citizen->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/cliente/incidencias', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['success', 'message', 'data' => ['errors']]);
    }

    private function createCitizen(string $email): Usuario
    {
        return Usuario::create([
            'nombre' => 'Ciudadano de prueba',
            'correo' => $email,
            'password' => 'password-segura',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
    }

    private function createIncident(Usuario $usuario): Reporte
    {
        $categoria = CategoriaReporte::create([
            'nombre' => 'Seguridad '.$usuario->id_usuario,
            'descripcion' => 'Categoría de prueba',
            'estado' => true,
        ]);

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

    private function createAdministrator(int $level): Administrador
    {
        return Administrador::create([
            'nombre_completo' => 'Administrador de prueba',
            'username' => 'admin-level-'.$level,
            'password' => 'password-segura',
            'estado' => true,
            'nivel' => $level,
        ]);
    }
}
