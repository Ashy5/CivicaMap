<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCitizenAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario || ! $usuario->estado) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción.',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
