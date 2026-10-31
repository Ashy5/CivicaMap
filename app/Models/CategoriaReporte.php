<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaReporte extends Model
{
    protected $table = 'categorias_reporte';

    protected $primaryKey = 'id_categoria';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function reportes()
    {
        return $this->hasMany(
            Reporte::class,
            'id_categoria',
            'id_categoria'
        );
    }
}