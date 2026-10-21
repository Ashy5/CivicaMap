<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_detalle', function (Blueprint $table) {
            $table->id('id_detalle');

            $table->unsignedBigInteger('id_solicitud');
            $table->unsignedBigInteger('id_servicio');

            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);

            $table->foreign('id_solicitud')
                ->references('id_solicitud')
                ->on('solicitudes')
                ->onDelete('cascade');

            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_detalle');
    }
};
