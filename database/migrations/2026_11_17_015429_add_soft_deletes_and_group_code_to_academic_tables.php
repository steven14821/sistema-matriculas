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
        foreach (['programas', 'asignaturas', 'periodos', 'estudiantes', 'grupos', 'matriculas'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->softDeletes();
                });
            }
        }

        if (! Schema::hasColumn('grupos', 'codigo')) {
            Schema::table('grupos', function (Blueprint $table): void {
                $table->string('codigo')->nullable();
            });
        }

        DB::table('grupos')
            ->whereNull('codigo')
            ->orderBy('id')
            ->chunkById(100, function ($groups): void {
                foreach ($groups as $group) {
                    DB::table('grupos')
                        ->where('id', $group->id)
                        ->update(['codigo' => 'GRP-'.$group->id]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('grupos', 'profesor_id')) {
            return;
        }

        foreach (['programas', 'asignaturas', 'periodos', 'estudiantes', 'grupos', 'matriculas'] as $tableName) {
            if (Schema::hasColumn($tableName, 'deleted_at')
                && DB::table($tableName)->whereNotNull('deleted_at')->exists()) {
                throw new RuntimeException('Academic soft-delete data exists and cannot be dropped safely.');
            }
        }

        if (Schema::hasColumn('grupos', 'codigo') && DB::table('grupos')->whereNotNull('codigo')->exists()) {
            throw new RuntimeException('Group codes exist and cannot be dropped safely.');
        }

        foreach (['programas', 'asignaturas', 'periodos', 'estudiantes', 'grupos', 'matriculas'] as $tableName) {
            if (Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropSoftDeletes();
                });
            }
        }

        if (Schema::hasColumn('grupos', 'codigo')) {
            Schema::table('grupos', function (Blueprint $table): void {
                $table->dropColumn('codigo');
            });
        }
    }
};
