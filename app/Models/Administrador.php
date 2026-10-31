<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Administrador extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'administradores';

    protected $fillable = [
        'nombre_completo',
        'username',
        'password',
        'estado',
        'nivel',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => 'boolean',
            'nivel' => 'integer',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'administrador_rol',
            'administrador_id',
            'id_rol'
        );
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = array_map(
            static fn (string $role): string => Str::lower($role),
            (array) $roles
        );

        return $this->roles->contains(
            static fn (Rol $role): bool => in_array(Str::lower($role->nombre), $roles, true)
        );
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();
    }
}
