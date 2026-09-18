<?php

namespace App\Http\Controllers;

use App\Models\CategoriaReporte;
use App\Models\Evidencia;
use App\Models\Reporte;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReporteController extends Controller
{
    public function index()
    {
        $reportes = Reporte::with(['usuario', 'categoria'])
            ->orderByDesc('fecha_reporte')
            ->get();

        return view('reportes.index', compact('reportes'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_usuario' => ['required', 'integer', 'exists:usuarios,id_usuario'],
                'id_categoria' => ['required', 'integer', 'exists:categorias_reporte,id_categoria'],
                'descripcion' => ['required', 'string', 'min:10', 'max:2000'],
                'latitud_aprox' => ['required', 'numeric', 'between:-90,90'],
                'longitud_aprox' => ['required', 'numeric', 'between:-180,180'],
                'fecha_reporte' => ['required', 'date_format:Y-m-d\TH:i'],
                'estado' => ['required', 'string', 'max:30'],
                'anonimizado' => ['required', 'boolean'],
                'evidencia' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $reporte = Reporte::create([
                'id_usuario' => $validated['id_usuario'],
                'id_categoria' => $validated['id_categoria'],
                'descripcion' => $validated['descripcion'],
                'latitud_aprox' => $validated['latitud_aprox'],
                'longitud_aprox' => $validated['longitud_aprox'],
                'fecha_reporte' => Carbon::parse($validated['fecha_reporte'])->format('Y-m-d H:i:s'),
                'estado' => $validated['estado'],
                'anonimizado' => (bool) $validated['anonimizado'],
            ]);

            if ($request->hasFile('evidencia')) {
                $archivo = $request->file('evidencia');
                $numero = Evidencia::where('id_reporte', $reporte->id_reporte)->count() + 1;
                $nombreArchivo = 'Reporte_' . $reporte->id_reporte . '_' . $numero . '.jpg';
                $rutaArchivo = 'reportes/' . $nombreArchivo;

                $contenido = file_get_contents($archivo->getRealPath());

                if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                    $imagen = @imagecreatefromstring($contenido);
                    if ($imagen !== false) {
                        $rutaTemporal = tempnam(sys_get_temp_dir(), 'reporte_');
                        imagejpeg($imagen, $rutaTemporal, 85);
                        imagedestroy($imagen);
                        $contenido = file_get_contents($rutaTemporal);
                        unlink($rutaTemporal);
                    }
                }

                Storage::disk('public')->put($rutaArchivo, $contenido);

                Evidencia::create([
                    'id_reporte' => $reporte->id_reporte,
                    'tipo_archivo' => 'image/jpeg',
                    'ruta_archivo' => $rutaArchivo,
                    'fecha_subida' => now(),
                ]);
            }

            return redirect()->route('reportes.index')->with('success', 'Reporte guardado exitosamente.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'No se pudo guardar el reporte. Verifica los campos que faltan o están incorrectos.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo guardar el reporte. Revisa que el usuario, la categoría, la imagen y los datos obligatorios estén correctos.');
        }
    }
}
