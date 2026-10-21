<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id('id_evidencia');

            $table->unsignedBigInteger('id_reporte');

            $table->string('tipo_archivo', 50);
            $table->string('ruta_archivo', 255);
            $table->dateTime('fecha_subida');

            $table->foreign('id_reporte')
                ->references('id_reporte')
                ->on('reportes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
