<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id_permission');
            $table->string('name', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_permission');
            $table->primary(['id_rol', 'id_permission']);

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('roles')
                ->cascadeOnDelete();

            $table->foreign('id_permission')
                ->references('id_permission')
                ->on('permissions')
                ->cascadeOnDelete();
        });

        Schema::create('administrador_rol', function (Blueprint $table) {
            $table->unsignedBigInteger('administrador_id');
            $table->unsignedBigInteger('id_rol');
            $table->primary(['administrador_id', 'id_rol']);

            $table->foreign('administrador_id')
                ->references('id')
                ->on('administradores')
                ->cascadeOnDelete();

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('roles')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrador_rol');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
    }
};
