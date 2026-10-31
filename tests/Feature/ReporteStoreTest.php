<?php

namespace Tests\Feature;

use App\Models\CategoriaReporte;
use App\Models\Evidencia;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReporteStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_report_uses_generated_user_when_no_user_exists(): void
    {
        Storage::fake('public');

        $categoria = CategoriaReporte::create([
            'nombre' => 'Seguridad',
            'descripcion' => 'Reporte de seguridad',
            'estado' => true,
        ]);

        $response = $this->post('/reportes', [
            'id_usuario' => 1,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Hay basura acumulada en la esquina principal.',
            'latitud_aprox' => 20.6597,
            'longitud_aprox' => -103.3496,
            'fecha_reporte' => now()->format('Y-m-d\TH:i'),
            'estado' => 'Recibido',
            'anonimizado' => true,
        ]);

        $response->assertRedirect('/reportes');
        $response->assertSessionHas('success', 'Reporte guardado exitosamente.');

        $this->assertDatabaseHas('usuarios', ['correo' => 'anonimo@civicamap.local']);
        $this->assertDatabaseHas('reportes', ['id_categoria' => $categoria->id_categoria, 'estado' => 'Recibido']);
    }

    public function test_user_can_edit_report_and_replace_image(): void
    {
        Storage::fake('public');

        $usuario = Usuario::create([
            'nombre' => 'Usuario prueba',
            'correo' => 'usuario-edit@test.com',
            'password' => 'secret',
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        $categoria = CategoriaReporte::create([
            'nombre' => 'Infraestructura',
            'descripcion' => 'Reporte de infraestructura',
            'estado' => true,
        ]);

        $reporte = Reporte::create([
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Bache grande en la avenida principal.',
            'latitud_aprox' => 20.7000,
            'longitud_aprox' => -103.3500,
            'fecha_reporte' => now(),
            'estado' => 'En revisión',
            'anonimizado' => false,
        ]);

        Evidencia::create([
            'id_reporte' => $reporte->id_reporte,
            'tipo_archivo' => 'image/jpeg',
            'ruta_archivo' => 'reportes/Reporte_' . $reporte->id_reporte . '_1.jpg',
            'fecha_subida' => now(),
        ]);

        Storage::disk('public')->put('reportes/Reporte_' . $reporte->id_reporte . '_1.jpg', 'fake-image-content');

        $response = $this->put('/reportes/' . $reporte->id_reporte, [
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Bache corregido en la avenida principal.',
            'latitud_aprox' => 20.7100,
            'longitud_aprox' => -103.3600,
            'fecha_reporte' => now()->format('Y-m-d\TH:i'),
            'estado' => 'Atendido',
            'anonimizado' => true,
            'evidencia' => UploadedFile::fake()->create('nueva-evidencia.jpg', 1024, 'image/jpeg'),
        ]);

        $response->assertRedirect('/reportes');
        $response->assertSessionHas('success', 'Reporte actualizado exitosamente.');
        $this->assertDatabaseHas('reportes', [
            'id_reporte' => $reporte->id_reporte,
            'descripcion' => 'Bache corregido en la avenida principal.',
            'estado' => 'Atendido',
        ]);
    }

    public function test_user_can_create_a_report_with_optional_image(): void
    {
        Storage::fake('public');

        $usuario = Usuario::create([
            'nombre' => 'Usuario prueba',
            'correo' => 'usuario@test.com',
            'password' => 'secret',
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        $categoria = CategoriaReporte::create([
            'nombre' => 'Seguridad',
            'descripcion' => 'Reporte de seguridad',
            'estado' => true,
        ]);

        $response = $this->post('/reportes', [
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Hay basura acumulada en la esquina principal.',
            'latitud_aprox' => 20.6597,
            'longitud_aprox' => -103.3496,
            'fecha_reporte' => now()->format('Y-m-d\TH:i'),
            'estado' => 'Recibido',
            'anonimizado' => true,
            'evidencia' => UploadedFile::fake()->create('evidencia.jpg', 1024, 'image/jpeg'),
        ]);

        $response->assertRedirect('/reportes');
        $response->assertSessionHas('success', 'Reporte guardado exitosamente.');

        $this->assertDatabaseHas('reportes', [
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'estado' => 'Recibido',
        ]);

        $this->assertDatabaseHas('evidencias', [
            'tipo_archivo' => 'image/jpeg',
        ]);

        $reporte = Reporte::first();
        $this->assertNotNull($reporte);
        $this->assertNotNull(Evidencia::where('id_reporte', $reporte->id_reporte)->first());
        Storage::disk('public')->assertExists('reportes/Reporte_' . $reporte->id_reporte . '_1.jpg');
    }

    public function test_report_list_shows_saved_report_information(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario prueba',
            'correo' => 'usuario2@test.com',
            'password' => 'secret',
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        $categoria = CategoriaReporte::create([
            'nombre' => 'Infraestructura',
            'descripcion' => 'Reporte de infraestructura',
            'estado' => true,
        ]);

        Reporte::create([
            'id_usuario' => $usuario->id_usuario,
            'id_categoria' => $categoria->id_categoria,
            'descripcion' => 'Bache grande en la avenida principal.',
            'latitud_aprox' => 20.7000,
            'longitud_aprox' => -103.3500,
            'fecha_reporte' => now(),
            'estado' => 'En revisión',
            'anonimizado' => false,
        ]);

        $response = $this->get('/reportes');

        $response->assertOk();
        $response->assertSeeText('Listado de reportes');
        $response->assertSeeText('Bache grande en la avenida principal.');
        $response->assertSeeText('Infraestructura');
        $response->assertSeeText('En revisión');
    }
}
