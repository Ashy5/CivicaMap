<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $primaryKey = 'id_evidencia';

    public $timestamps = false;

    protected $fillable = [
        'id_reporte',
        'tipo_archivo',
        'ruta_archivo',
        'fecha_subida',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    public function reporte()
    {
        return $this->belongsTo(
            Reporte::class,
            'id_reporte',
            'id_reporte'
        );
    }
}