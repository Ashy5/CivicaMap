<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cliente\StoreIncidenciaRequest;
use App\Http\Requests\Api\Cliente\UpdateIncidenciaEstadoRequest;
use App\Http\Requests\Api\Cliente\UpdateIncidenciaRequest;
use App\Models\Reporte;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class IncidenciaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuario = $this->usuario($request);
        Gate::forUser($usuario)->authorize('viewAny', Reporte::class);

        $incidencias = ($usuario->hasAnyRole(['Seguridad', 'Admin', 'SuperAdmin'])
            || $usuario->hasPermissionTo('incidencias.viewAny')
            ? Reporte::query()
            : $usuario->reportes())
            ->with(['categoria', 'usuario'])
            ->orderByDesc('fecha_reporte')
            ->get();

        return $this->success('Incidencias obtenidas correctamente.', $incidencias);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $usuario = $this->usuario($request);
        $esLectorGlobal = $usuario->hasAnyRole(['Seguridad', 'Admin', 'SuperAdmin'])
            || $usuario->hasPermissionTo('incidencias.viewAny');
        $incidencia = ($esLectorGlobal ? Reporte::query() : $usuario->reportes())
            ->with(['categoria', 'usuario'])
            ->findOrFail($id);

        Gate::forUser($usuario)->authorize('view', $incidencia);

        return $this->success('Incidencia obtenida correctamente.', $incidencia);
    }

    public function store(StoreIncidenciaRequest $request): JsonResponse
    {
        $incidencia = $this->usuario($request)->reportes()->create($request->validated() + [
            'fecha_reporte' => now(),
            'estado' => 'Recibido',
            'anonimizado' => false,
        ]);

        return $this->success(
            'Incidencia creada correctamente.',
            $incidencia->load('categoria'),
            201
        );
    }

    public function update(UpdateIncidenciaRequest $request, string $id): JsonResponse
    {
        $incidencia = $this->usuario($request)->reportes()->findOrFail($id);
        $incidencia->update($request->validated());

        return $this->success(
            'Incidencia actualizada correctamente.',
            $incidencia->fresh('categoria')
        );
    }

    public function updateStatus(UpdateIncidenciaEstadoRequest $request, string $id): JsonResponse
    {
        $usuario = $this->usuario($request);
        $incidencia = ($usuario->hasAnyRole(['Seguridad', 'Admin', 'SuperAdmin'])
            || $usuario->hasPermissionTo('incidencias.viewAny')
            ? Reporte::query()
            : $usuario->reportes())
            ->findOrFail($id);

        Gate::forUser($usuario)->authorize('updateStatus', $incidencia);
        $incidencia->update($request->validated());

        return $this->success(
            'Estado de incidencia actualizado correctamente.',
            $incidencia->fresh(['categoria', 'usuario'])
        );
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $incidencia = $this->usuario($request)->reportes()
            ->with('evidencias')
            ->findOrFail($id);

        foreach ($incidencia->evidencias as $evidencia) {
            if ($evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)) {
                Storage::disk('public')->delete($evidencia->ruta_archivo);
            }
        }

        $incidencia->delete();

        return $this->success('Incidencia eliminada correctamente.');
    }

    private function usuario(Request $request): Usuario
    {
        return $request->user();
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
