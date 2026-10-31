<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'estado' => 'boolean',
        'password' => 'hashed',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'usuario_rol',
            'id_usuario',
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

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();
    }

    public function autenticaciones(): HasMany
    {
        return $this->hasMany(
            Autenticacion::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(
            Reporte::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function listaDeseos(): HasMany
    {
        return $this->hasMany(
            ListaDeseo::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function carritos(): HasMany
    {
        return $this->hasMany(
            Carrito::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(
            Solicitud::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(
            Log::class,
            'id_usuario',
            'id_usuario'
        );
    }
}
