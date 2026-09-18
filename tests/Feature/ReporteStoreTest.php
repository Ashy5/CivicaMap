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
