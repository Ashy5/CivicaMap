<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreIncidenciaRequest;
use App\Http\Requests\Api\Admin\UpdateIncidenciaRequest;
use App\Models\Reporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class IncidenciaController extends Controller
{
    public function index(): JsonResponse
    {
        $incidencias = Reporte::with(['usuario', 'categoria'])
            ->orderByDesc('fecha_reporte')
            ->get();

        return $this->success('Incidencias obtenidas correctamente.', $incidencias);
    }

    public function show(string $id): JsonResponse
    {
        return $this->success(
            'Incidencia obtenida correctamente.',
            Reporte::with(['usuario', 'categoria'])->findOrFail($id)
        );
    }

    public function store(StoreIncidenciaRequest $request): JsonResponse
    {
        $incidencia = Reporte::create($request->validated() + [
            'fecha_reporte' => now(),
            'estado' => 'Recibido',
            'anonimizado' => false,
        ]);

        return $this->success(
            'Incidencia creada correctamente.',
            $incidencia->load(['usuario', 'categoria']),
            201
        );
    }

    public function update(UpdateIncidenciaRequest $request, string $id): JsonResponse
    {
        $incidencia = Reporte::findOrFail($id);
        $incidencia->update($request->validated());

        return $this->success(
            'Incidencia actualizada correctamente.',
            $incidencia->fresh(['usuario', 'categoria'])
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $incidencia = Reporte::with('evidencias')->findOrFail($id);

        foreach ($incidencia->evidencias as $evidencia) {
            if ($evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)) {
                Storage::disk('public')->delete($evidencia->ruta_archivo);
            }
        }

        $incidencia->delete();

        return $this->success('Incidencia eliminada correctamente.');
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
