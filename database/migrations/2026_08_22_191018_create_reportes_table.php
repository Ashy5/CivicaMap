<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id('id_reporte');

            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_categoria');

            $table->text('descripcion');

            $table->decimal('latitud_aprox', 10, 7);
            $table->decimal('longitud_aprox', 10, 7);

            $table->dateTime('fecha_reporte');

            $table->string('estado', 30);

            $table->boolean('anonimizado');

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias_reporte')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
