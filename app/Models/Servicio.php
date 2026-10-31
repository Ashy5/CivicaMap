<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $primaryKey = 'id_servicio';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'costo',
        'estado',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function carritoDetalles()
    {
        return $this->hasMany(
            CarritoDetalle::class,
            'id_servicio',
            'id_servicio'
        );
    }
    
    public function listaDeseos()
    {
        return $this->hasMany(
            ListaDeseo::class,
            'id_servicio',
            'id_servicio'
        );
    }

    public function solicitudDetalles()
    {
        return $this->hasMany(
            SolicitudDetalle::class,
            'id_servicio',
            'id_servicio'
        );
    }
}