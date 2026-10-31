<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CategoriaReporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::forUser($request->user())->authorize('viewAny', CategoriaReporte::class);

        return response()->json([
            'success' => true,
            'message' => 'Categorías obtenidas correctamente.',
            'data' => CategoriaReporte::orderBy('nombre')->get(),
        ]);
    }
}
