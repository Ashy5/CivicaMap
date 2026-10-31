<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autenticaciones', function (Blueprint $table) {
            $table->id('id_autenticacion');
            $table->unsignedBigInteger('id_usuario');
            $table->string('proveedor', 50);
            $table->string('identificador_externo', 255)->nullable();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autenticaciones');
    }
};