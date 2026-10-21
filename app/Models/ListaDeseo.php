<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaDeseo extends Model
{
    protected $table = 'lista_deseos';

    protected $primaryKey = 'id_deseo';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_servicio',
        'fecha_agregado',
    ];

    protected $casts = [
        'fecha_agregado' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
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
