<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('nivel');
            $table->timestamps();
        });

        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_id')->constrained('programas')->restrictOnDelete();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->unsignedTinyInteger('creditos');
            $table->timestamps();
        });

        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('abierto')->default(false);
            $table->timestamps();
        });

        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $table->string('codigo')->unique();
            $table->string('documento')->unique();
            $table->foreignId('programa_id')->constrained('programas')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignatura_id')->constrained('asignaturas')->restrictOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->restrictOnDelete();
            $table->string('docente');
            $table->string('horario');
            $table->unsignedInteger('cupo');
            $table->timestamps();
        });

        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->restrictOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->restrictOnDelete();
            $table->string('estado');
            $table->date('fecha');
            $table->timestamps();
            $table->unique(['estudiante_id', 'grupo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('estudiantes');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('asignaturas');
        Schema::dropIfExists('programas');
    }
};
