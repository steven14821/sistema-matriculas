<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table): void {
            $table->foreignId('programa_id')->nullable()->change();
        });

        Schema::table('periodos', function (Blueprint $table): void {
            $table->unsignedSmallInteger('limite_creditos')->nullable()->after('abierto');
        });

        Schema::create('solicitudes_programa', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('estudiante_id')->constrained()->restrictOnDelete();
            $table->foreignId('programa_id')->constrained()->restrictOnDelete();
            $table->string('comprobante_path');
            $table->string('estado')->default('pendiente');
            $table->text('observacion')->nullable();
            $table->foreignId('revisado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_en')->nullable();
            $table->timestamps();
            $table->index(['estudiante_id', 'estado']);
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        if (DB::table('solicitudes_programa')->exists()) {
            throw new RuntimeException('Program requests exist and cannot be dropped safely.');
        }

        if (DB::table('estudiantes')->whereNull('programa_id')->exists()) {
            throw new RuntimeException('Students without a program exist and cannot be rolled back safely.');
        }

        Schema::table('estudiantes', function (Blueprint $table): void {
            $table->foreignId('programa_id')->nullable(false)->change();
        });

        Schema::table('periodos', function (Blueprint $table): void {
            $table->dropColumn('limite_creditos');
        });

        Schema::dropIfExists('solicitudes_programa');
    }
};
