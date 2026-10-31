<?php

namespace App\Http\Middleware;

use App\Models\Administrador;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministratorRole
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $administrator = $request->user();

        if (! $administrator instanceof Administrador || ! $administrator->estado) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción.',
                'data' => null,
            ], 403);
        }

        if (! Gate::forUser($administrator)->allows($ability)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción.',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
