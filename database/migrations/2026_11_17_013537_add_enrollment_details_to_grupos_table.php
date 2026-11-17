<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToAdd = [];

        if (! Schema::hasColumn('grupos', 'docente')) {
            $columnsToAdd[] = 'docente';
        }

        if (! Schema::hasColumn('grupos', 'horario')) {
            $columnsToAdd[] = 'horario';
        }

        if ($columnsToAdd === []) {
            return;
        }

        Schema::table('grupos', function (Blueprint $table) use ($columnsToAdd): void {
            if (in_array('docente', $columnsToAdd, true)) {
                $table->string('docente')->nullable();
            }

            if (in_array('horario', $columnsToAdd, true)) {
                $table->string('horario')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('grupos', 'codigo') || ! Schema::hasColumn('grupos', 'profesor_id')) {
            return;
        }

        $columnsToDrop = array_values(array_filter(
            ['docente', 'horario'],
            fn (string $column): bool => Schema::hasColumn('grupos', $column),
        ));

        if ($columnsToDrop === []) {
            return;
        }

        foreach ($columnsToDrop as $column) {
            if (DB::table('grupos')->whereNotNull($column)->exists()) {
                throw new RuntimeException('Enrollment detail columns contain data and cannot be dropped safely.');
            }
        }

        Schema::table('grupos', function (Blueprint $table) use ($columnsToDrop): void {
            $table->dropColumn($columnsToDrop);
        });
    }
};
