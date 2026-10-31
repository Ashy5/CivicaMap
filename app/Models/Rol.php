<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id_rol';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuario_rol',
            'id_rol',
            'id_usuario'
        );
    }

    public function administradores(): BelongsToMany
    {
        return $this->belongsToMany(
            Administrador::class,
            'administrador_rol',
            'id_rol',
            'administrador_id'
        );
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'id_rol',
            'id_permission'
        );
    }
}
