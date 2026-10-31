<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalle extends Model
{
    protected $table = 'carrito_detalle';

    protected $primaryKey = 'id_detalle';

    public $timestamps = false;

    protected $fillable = [
        'id_carrito',
        'id_servicio',
        'cantidad',
    ];

    public function carrito()
    {
        return $this->belongsTo(
            Carrito::class,
            'id_carrito',
            'id_carrito'
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