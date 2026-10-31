<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreRoleRequest;
use App\Http\Requests\Api\Admin\SyncRoleAssignmentRequest;
use App\Models\Administrador;
use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->success(
            'Roles obtenidos correctamente.',
            Rol::with('permissions')->orderBy('nombre')->get()
        );
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $role = DB::transaction(function () use ($validated): Rol {
            $role = Rol::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            $permissions = collect($validated['permissions'] ?? [])
                ->map(fn (string $name): Permission => Permission::firstOrCreate(['name' => $name]));

            $role->permissions()->sync($permissions->pluck('id_permission')->all());

            return $role->load('permissions');
        });

        return $this->success('Rol creado correctamente.', $role, 201);
    }

    public function syncAdministratorRoles(SyncRoleAssignmentRequest $request, string $id): JsonResponse
    {
        $administrator = Administrador::findOrFail($id);
        $administrator->roles()->sync($request->validated('role_ids'));

        return $this->success(
            'Roles del administrador actualizados correctamente.',
            $administrator->load('roles')
        );
    }

    public function removeAdministratorRole(string $id, string $roleId): JsonResponse
    {
        $administrator = Administrador::findOrFail($id);
        $role = $administrator->roles()->where('roles.id_rol', $roleId)->firstOrFail();
        $administrator->roles()->detach($role->id_rol);

        return $this->success(
            'Rol removido del administrador correctamente.',
            $administrator->load('roles')
        );
    }

    private function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
