<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autenticacion extends Model
{
    protected $table = 'autenticaciones';

    protected $primaryKey = 'id_autenticacion';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'proveedor',
        'identificador_externo',
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }
}
