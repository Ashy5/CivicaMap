<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

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
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'usuario_rol',
            'id_usuario',
            'id_rol'
        );
    }

    public function autenticaciones()
    {
        return $this->hasMany(
            Autenticacion::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function reportes()
    {
        return $this->hasMany(
            Reporte::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function listaDeseos()
    {
        return $this->hasMany(
            ListaDeseo::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function carritos()
    {
        return $this->hasMany(
            Carrito::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function solicitudes()
    {
        return $this->hasMany(
            Solicitud::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function logs()
    {
        return $this->hasMany(
            Log::class,
            'id_usuario',
            'id_usuario'
        );
    }
}