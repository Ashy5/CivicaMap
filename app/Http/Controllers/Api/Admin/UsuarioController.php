<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AdminUserStatusRequest;
use App\Http\Requests\Api\Admin\StoreUsuarioRequest;
use App\Http\Requests\Api\Admin\SyncRoleAssignmentRequest;
use App\Http\Requests\Api\Admin\UpdateUsuarioRequest;
use App\Http\Resources\Api\Admin\AdminUserResource;
use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsuarioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Usuario::class);

        $usuarios = Usuario::with('roles')->get()
            ->concat(Administrador::with('roles')->get())
            ->sortBy(fn (Usuario|Administrador $usuario): string => mb_strtolower(
                $usuario instanceof Administrador ? $usuario->nombre_completo : $usuario->nombre
            ))
            ->values();

        return AdminUserResource::collection($usuarios)->additional([
            'success' => true,
            'message' => 'Usuarios obtenidos correctamente.',
        ]);
    }

    public function show(string $id): AdminUserResource
    {
        $usuario = Usuario::with('roles')->findOrFail($id);
        $this->authorize('view', $usuario);

        return (new AdminUserResource($usuario))->additional([
            'success' => true,
            'message' => 'Usuario obtenido correctamente.',
        ]);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $this->authorize('create', Usuario::class);

        $usuario = Usuario::create($request->validated() + [
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        return (new AdminUserResource($usuario->load('roles')))
            ->additional([
                'success' => true,
                'message' => 'Usuario creado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateUsuarioRequest $request, string $id): AdminUserResource
    {
        $usuario = Usuario::findOrFail($id);
        $this->authorize('update', $usuario);
        $usuario->update($request->validated());

        return (new AdminUserResource($usuario->fresh()->load('roles')))->additional([
            'success' => true,
            'message' => 'Usuario actualizado correctamente.',
        ]);
    }

    public function destroy(string $id): AdminUserResource
    {
        $usuario = Usuario::findOrFail($id);
        $this->authorize('delete', $usuario);
        $usuario->update(['estado' => false]);
        $usuario->tokens()->delete();

        return (new AdminUserResource($usuario->fresh()->load('roles')))->additional([
            'success' => true,
            'message' => 'Usuario desactivado correctamente.',
        ]);
    }

    public function updateStatus(AdminUserStatusRequest $request, string $id): AdminUserResource
    {
        $validated = $request->validated();
        $usuario = $validated['tipo_usuario'] === 'administrador'
            ? Administrador::findOrFail($id)
            : Usuario::findOrFail($id);
        $this->authorize('update', $usuario);

        if (array_key_exists('estado', $validated)) {
            $usuario->estado = $validated['estado'];
        }

        if (array_key_exists('role_ids', $validated)) {
            $usuario->roles()->sync($validated['role_ids']);
        }

        $usuario->save();

        if (array_key_exists('estado', $validated) && ! $usuario->estado) {
            $usuario->tokens()->delete();
        }

        return (new AdminUserResource($usuario->fresh()->load('roles')))->additional([
            'success' => true,
            'message' => 'Estado o roles del usuario actualizados correctamente.',
        ]);
    }

    public function syncRoles(SyncRoleAssignmentRequest $request, string $id): AdminUserResource
    {
        $usuario = Usuario::findOrFail($id);
        $this->authorize('update', $usuario);
        $usuario->roles()->sync($request->validated('role_ids'));

        return (new AdminUserResource($usuario->load('roles')))->additional([
            'success' => true,
            'message' => 'Roles del usuario actualizados correctamente.',
        ]);
    }

    public function removeRole(string $id, string $roleId): AdminUserResource
    {
        $usuario = Usuario::findOrFail($id);
        $this->authorize('update', $usuario);
        $role = $usuario->roles()->where('roles.id_rol', $roleId)->firstOrFail();
        $usuario->roles()->detach($role->id_rol);

        return (new AdminUserResource($usuario->load('roles')))->additional([
            'success' => true,
            'message' => 'Rol removido del usuario correctamente.',
        ]);
    }
}
