<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_deseos', function (Blueprint $table) {
            $table->id('id_deseo');

            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_servicio');

            $table->dateTime('fecha_agregado');

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');

            $table->unique(['id_usuario', 'id_servicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_deseos');
    }
};
