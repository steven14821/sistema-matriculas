<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::table('grupos', function (Blueprint $table): void {
            $table->string('codigo')->nullable()->after('id');
            $table->unsignedBigInteger('profesor_id')->nullable()->after('codigo');
        });

        DB::table('grupos')
            ->select(['id', 'docente'])
            ->orderBy('id')
            ->get()
            ->each(function (object $grupo): void {
                $nombre = trim($grupo->docente);
                $profesorId = null;

                if ($nombre !== '') {
                    $profesorId = DB::table('profesores')
                        ->where('nombre', $nombre)
                        ->value('id');

                    if ($profesorId === null) {
                        $profesorId = DB::table('profesores')->insertGetId([
                            'nombre' => $nombre,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::table('grupos')
                    ->where('id', $grupo->id)
                    ->update([
                        'codigo' => 'A'.$grupo->id,
                        'profesor_id' => $profesorId,
                    ]);
            });

        Schema::table('grupos', function (Blueprint $table): void {
            $table->foreign('profesor_id')->references('id')->on('profesores')->nullOnDelete();
            $table->string('codigo')->nullable(false)->change();
            $table->unique(
                ['asignatura_id', 'periodo_id', 'codigo'],
                'grupos_asignatura_periodo_codigo_unique',
            );
            $table->dropColumn(['docente', 'horario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table): void {
            $table->dropUnique('grupos_asignatura_periodo_codigo_unique');
            $table->dropForeign(['profesor_id']);
            $table->string('docente')->nullable();
            $table->string('horario')->nullable();
        });

        DB::table('grupos')
            ->join('profesores', 'grupos.profesor_id', '=', 'profesores.id')
            ->update(['grupos.docente' => DB::raw('profesores.nombre')]);

        Schema::table('grupos', function (Blueprint $table): void {
            $table->dropColumn(['codigo', 'profesor_id']);
        });

        Schema::dropIfExists('profesores');
    }
};
