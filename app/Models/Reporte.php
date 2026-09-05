<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_categoria',
        'descripcion',
        'latitud_aprox',
        'longitud_aprox',
        'fecha_reporte',
        'estado',
        'anonimizado',
    ];

    protected $casts = [
        'latitud_aprox' => 'decimal:7',
        'longitud_aprox' => 'decimal:7',
        'fecha_reporte' => 'datetime',
        'anonimizado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(
            CategoriaReporte::class,
            'id_categoria',
            'id_categoria'
        );
    }

    public function evidencias()
    {
        return $this->hasMany(
            Evidencia::class,
            'id_reporte',
            'id_reporte'
        );
    }
}
