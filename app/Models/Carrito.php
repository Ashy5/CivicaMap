<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carritos';

    protected $primaryKey = 'id_carrito';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha_creacion',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function detalles()
    {
        return $this->hasMany(
            CarritoDetalle::class,
            'id_carrito',
            'id_carrito'
        );
    }
}
