<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreCategoriaRequest;
use App\Http\Requests\Api\Admin\UpdateCategoriaRequest;
use App\Models\CategoriaReporte;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->success('Categorías obtenidas correctamente.', CategoriaReporte::all());
    }

    public function show(string $id): JsonResponse
    {
        return $this->success('Categoría obtenida correctamente.', CategoriaReporte::findOrFail($id));
    }

    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = CategoriaReporte::create($request->validated() + ['estado' => true]);

        return $this->success('Categoría creada correctamente.', $categoria, 201);
    }

    public function update(UpdateCategoriaRequest $request, string $id): JsonResponse
    {
        $categoria = CategoriaReporte::findOrFail($id);
        $categoria->update($request->validated());

        return $this->success('Categoría actualizada correctamente.', $categoria->fresh());
    }

    public function destroy(string $id): JsonResponse
    {
        $categoria = CategoriaReporte::findOrFail($id);
        $categoria->update(['estado' => false]);

        return $this->success('Categoría desactivada correctamente.', $categoria->fresh());
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
