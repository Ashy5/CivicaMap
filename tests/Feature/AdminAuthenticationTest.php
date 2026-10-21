<?php

namespace Tests\Feature;

use App\Models\Administrador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_available(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('name="username"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_active_admin_can_log_in_and_open_the_dashboard(): void
    {
        $admin = $this->createAdmin();

        $this->post(route('admin.login.store'), [
            'username' => $admin->username,
            'password' => 'secret',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success', 'Has iniciado sesión correctamente.');

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Panel administrativo')
            ->assertSeeText($admin->nombre_completo);
    }

    public function test_inactive_admin_is_logged_out_and_shown_an_error(): void
    {
        $admin = $this->createAdmin(estado: false);

        $this->post(route('admin.login.store'), [
            'username' => $admin->username,
            'password' => 'secret',
        ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error', 'Tu cuenta se encuentra deshabilitada.');

        $this->assertGuest('admin');
    }

    public function test_login_requires_username_and_password(): void
    {
        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['username', 'password']);
    }

    public function test_admin_dashboard_redirects_to_admin_login_for_guests(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_log_out(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('success', 'Has cerrado sesión correctamente.');

        $this->assertGuest('admin');
    }

    private function createAdmin(bool $estado = true): Administrador
    {
        return Administrador::create([
            'nombre_completo' => 'Administradora de prueba',
            'username' => 'admin-test',
            'password' => 'secret',
            'estado' => $estado,
        ]);
    }
}
