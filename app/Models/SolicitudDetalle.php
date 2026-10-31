<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDetalle extends Model
{
    protected $table = 'solicitud_detalle';

    protected $primaryKey = 'id_detalle';

    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'id_servicio',
        'cantidad',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function solicitud()
    {
        return $this->belongsTo(
            Solicitud::class,
            'id_solicitud',
            'id_solicitud'
        );
    }

    public function servicio()
    {
        return $this->belongsTo(
            Servicio::class,
            'id_servicio',
            'id_servicio'
        );
    }
}