<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminResourceStoreRequest;
use App\Http\Requests\Api\Admin\AdminResourceUpdateRequest;
use App\Http\Resources\Api\Admin\ServicioResource;
use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServicioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Servicio::class);

        return ServicioResource::collection(
            Servicio::query()->orderBy('nombre')->get()
        )->additional([
            'success' => true,
            'message' => 'Recursos obtenidos correctamente.',
        ]);
    }

    public function show(string $id): ServicioResource
    {
        $servicio = Servicio::findOrFail($id);
        $this->authorize('view', $servicio);

        return (new ServicioResource($servicio))->additional([
            'success' => true,
            'message' => 'Recurso obtenido correctamente.',
        ]);
    }

    public function store(AdminResourceStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Servicio::class);

        $servicio = Servicio::create($request->validated() + [
            'estado' => true,
        ]);

        return (new ServicioResource($servicio))
            ->additional([
                'success' => true,
                'message' => 'Recurso creado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function update(AdminResourceUpdateRequest $request, string $id): ServicioResource
    {
        $servicio = Servicio::findOrFail($id);
        $this->authorize('update', $servicio);
        $servicio->update($request->validated());

        return new ServicioResource($servicio->fresh())->additional([
            'success' => true,
            'message' => 'Recurso actualizado correctamente.',
        ]);
    }

    public function destroy(string $id): ServicioResource
    {
        $servicio = Servicio::findOrFail($id);
        $this->authorize('delete', $servicio);
        $servicio->update(['estado' => false]);

        return (new ServicioResource($servicio->fresh()))->additional([
            'success' => true,
            'message' => 'Recurso deshabilitado correctamente.',
        ]);
    }
}
