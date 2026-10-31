<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreUsuarioRequest;
use App\Http\Requests\Api\Admin\SyncRoleAssignmentRequest;
use App\Http\Requests\Api\Admin\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->success('Usuarios obtenidos correctamente.', Usuario::with('roles')->orderBy('nombre')->get());
    }

    public function show(string $id): JsonResponse
    {
        return $this->success('Usuario obtenido correctamente.', Usuario::with('roles')->findOrFail($id));
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = Usuario::create($request->validated() + [
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        return $this->success('Usuario creado correctamente.', $usuario, 201);
    }

    public function update(UpdateUsuarioRequest $request, string $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->validated());

        return $this->success('Usuario actualizado correctamente.', $usuario->fresh());
    }

    public function destroy(string $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update(['estado' => false]);
        $usuario->tokens()->delete();

        return $this->success('Usuario desactivado correctamente.', $usuario->fresh());
    }

    public function syncRoles(SyncRoleAssignmentRequest $request, string $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->roles()->sync($request->validated('role_ids'));

        return $this->success(
            'Roles del usuario actualizados correctamente.',
            $usuario->load('roles')
        );
    }

    public function removeRole(string $id, string $roleId): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $role = $usuario->roles()->where('roles.id_rol', $roleId)->firstOrFail();
        $usuario->roles()->detach($role->id_rol);

        return $this->success(
            'Rol removido del usuario correctamente.',
            $usuario->load('roles')
        );
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
