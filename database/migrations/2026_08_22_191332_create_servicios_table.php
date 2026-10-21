<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id('id_servicio');
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 80);
            $table->decimal('costo', 10, 2);
            $table->boolean('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
