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
        $reportes = Reporte::with(['usuario', 'categoria', 'evidencias'])
            ->orderByDesc('fecha_reporte')
            ->get();

        return view('reportes.index', compact('reportes'));
    }

    public function edit($id)
    {
        $reporte = Reporte::with(['categoria', 'evidencias'])->find($id);

        if (!$reporte) {
            return redirect()->route('reportes.index')
                ->with('error', 'El reporte que intentas editar no existe.');
        }

        $categorias = CategoriaReporte::where('estado', true)->get();

        return view('reportes.edit', compact('reporte', 'categorias'));
    }

    public function destroy($id)
    {
        $reporte = Reporte::with('evidencias')->find($id);

        if (!$reporte) {
            return redirect()->route('reportes.index')
                ->with('error', 'El reporte que intentas eliminar no existe.');
        }

        foreach ($reporte->evidencias as $evidencia) {
            if ($evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)) {
                Storage::disk('public')->delete($evidencia->ruta_archivo);
            }

            $evidencia->delete();
        }

        $reporte->delete();

        return redirect()->route('reportes.index')->with('success', 'Reporte eliminado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $reporte = Reporte::find($id);

        if (!$reporte) {
            return redirect()->route('reportes.index')
                ->with('error', 'El reporte que intentas actualizar no existe.');
        }

        try {
            $idUsuario = $request->input('id_usuario', $reporte->id_usuario);

            if (empty($idUsuario) || !Usuario::whereKey($idUsuario)->exists()) {
                $usuarioAnonimo = Usuario::firstOrCreate(
                    ['correo' => 'anonimo@civicamap.local'],
                    [
                        'nombre' => 'Usuario anónimo',
                        'password' => null,
                        'fecha_registro' => now(),
                        'estado' => true,
                    ]
                );

                $idUsuario = $usuarioAnonimo->id_usuario;
            }

            $validated = $request->validate([
                'id_usuario' => ['nullable', 'integer'],
                'id_categoria' => ['nullable', 'integer', 'exists:categorias_reporte,id_categoria'],
                'descripcion' => ['nullable', 'string', 'min:10', 'max:2000'],
                'latitud_aprox' => ['nullable', 'numeric', 'between:-90,90'],
                'longitud_aprox' => ['nullable', 'numeric', 'between:-180,180'],
                'fecha_reporte' => ['nullable', 'date_format:Y-m-d\TH:i'],
                'estado' => ['nullable', 'string', 'max:30'],
                'anonimizado' => ['nullable', 'boolean'],
                'evidencia' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $datosReporte = [
                'id_usuario' => $idUsuario,
                'id_categoria' => $request->filled('id_categoria') ? $validated['id_categoria'] : $reporte->id_categoria,
                'descripcion' => $request->filled('descripcion') ? $validated['descripcion'] : $reporte->descripcion,
                'latitud_aprox' => $request->filled('latitud_aprox') ? $validated['latitud_aprox'] : $reporte->latitud_aprox,
                'longitud_aprox' => $request->filled('longitud_aprox') ? $validated['longitud_aprox'] : $reporte->longitud_aprox,
                'fecha_reporte' => $request->filled('fecha_reporte') ? Carbon::parse($validated['fecha_reporte'])->format('Y-m-d H:i:s') : $reporte->fecha_reporte,
                'estado' => $request->filled('estado') ? $validated['estado'] : $reporte->estado,
                'anonimizado' => $request->has('anonimizado') ? (bool) $validated['anonimizado'] : (bool) $reporte->anonimizado,
            ];

            $reporte->update($datosReporte);

            if ($request->hasFile('evidencia')) {
                $archivo = $request->file('evidencia');
                $evidenciaActual = $reporte->evidencias()->latest('fecha_subida')->first();
                $numero = $reporte->evidencias()->count() + 1;
                $ext = strtolower($archivo->getClientOriginalExtension() ?: 'jpg');
                $rutaArchivo = 'reportes/Reporte_' . $reporte->id_reporte . '_' . $numero . '.' . $ext;

                if ($evidenciaActual && $evidenciaActual->ruta_archivo && Storage::disk('public')->exists($evidenciaActual->ruta_archivo)) {
                    Storage::disk('public')->delete($evidenciaActual->ruta_archivo);
                }

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

                if ($evidenciaActual) {
                    $evidenciaActual->update([
                        'tipo_archivo' => 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                        'ruta_archivo' => $rutaArchivo,
                        'fecha_subida' => now(),
                    ]);
                } else {
                    Evidencia::create([
                        'id_reporte' => $reporte->id_reporte,
                        'tipo_archivo' => 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                        'ruta_archivo' => $rutaArchivo,
                        'fecha_subida' => now(),
                    ]);
                }
            }

            return redirect()->route('reportes.index')->with('success', 'Reporte actualizado exitosamente.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'No se pudo actualizar el reporte. Revisa los datos ingresados.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el reporte. Verifica la información o la imagen enviada.');
        }
    }

    public function store(Request $request)
    {
        try {
            $idUsuario = $request->input('id_usuario');

            if (empty($idUsuario) || !Usuario::whereKey($idUsuario)->exists()) {
                $usuarioAnonimo = Usuario::firstOrCreate(
                    ['correo' => 'anonimo@civicamap.local'],
                    [
                        'nombre' => 'Usuario anónimo',
                        'password' => null,
                        'fecha_registro' => now(),
                        'estado' => true,
                    ]
                );

                $idUsuario = $usuarioAnonimo->id_usuario;
            }

            $validated = $request->validate([
                'id_usuario' => ['nullable', 'integer'],
                'id_categoria' => ['required', 'integer', 'exists:categorias_reporte,id_categoria'],
                'descripcion' => ['required', 'string', 'min:10', 'max:2000'],
                'latitud_aprox' => ['required', 'numeric', 'between:-90,90'],
                'longitud_aprox' => ['required', 'numeric', 'between:-180,180'],
                'fecha_reporte' => ['required', 'date_format:Y-m-d\TH:i'],
                'estado' => ['required', 'string', 'max:30'],
                'anonimizado' => ['required', 'boolean'],
                'evidencia' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $validated['id_usuario'] = $idUsuario;

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
