<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminLoginRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $usuario = Usuario::where('correo', $request->validated('correo'))->first();

        if (! $usuario || ! Hash::check($request->validated('password'), $usuario->password)) {
            return $this->error('Las credenciales proporcionadas no son válidas.', 401);
        }

        if (! $usuario->estado) {
            return $this->error('La cuenta se encuentra deshabilitada.', 403);
        }

        return $this->tokenResponse($usuario, 'Sesión iniciada correctamente.');
    }

    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $administrador = Administrador::where('username', $request->validated('username'))->first();

        if (! $administrador || ! Hash::check($request->validated('password'), $administrador->password)) {
            return $this->error('Las credenciales proporcionadas no son válidas.', 401);
        }

        if (! $administrador->estado) {
            return $this->error('La cuenta se encuentra deshabilitada.', 403);
        }

        return $this->tokenResponse($administrador, 'Sesión administrativa iniciada correctamente.');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $usuario = Usuario::create([
            ...$request->safe()->only(['nombre', 'correo', 'password']),
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        return $this->tokenResponse($usuario, 'Cuenta creada correctamente.', 201);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $user instanceof Administrador
            ? $user->only(['id', 'nombre_completo', 'username', 'nivel', 'estado'])
            : $user->only(['id_usuario', 'nombre', 'correo', 'fecha_registro', 'estado']);

        return $this->success('Perfil obtenido correctamente.', $data);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return $this->success('Sesión cerrada correctamente.');
    }

    private function tokenResponse(Usuario|Administrador $user, string $message, int $status = 200): JsonResponse
    {
        $data = [
            'token' => $user->createToken('api')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user instanceof Administrador
                ? $user->only(['id', 'nombre_completo', 'username', 'nivel'])
                : $user->only(['id_usuario', 'nombre', 'correo']),
        ];

        return $this->success($message, $data, $status);
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'data' => null], $status);
    }
}
