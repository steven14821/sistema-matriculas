<?php

namespace App\Actions;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollStudentInGroup
{
    public function handle(Estudiante $estudiante, Grupo $grupo): Matricula
    {
        return DB::transaction(function () use ($estudiante, $grupo): Matricula {
            $lockedGroup = Grupo::query()
                ->whereKey($grupo->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $lockedStudent = Estudiante::query()
                ->whereKey($estudiante->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $period = Periodo::query()
                ->whereKey($lockedGroup->periodo_id)
                ->lockForUpdate()
                ->firstOrFail();
            $today = today();

            if (! $period->abierto || $today->lt($period->fecha_inicio) || $today->gt($period->fecha_fin)) {
                throw ValidationException::withMessages([
                    'grupo' => 'El periodo académico no está habilitado para matrículas.',
                ]);
            }

            if (! $lockedGroup->asignatura()->where('programa_id', $lockedStudent->programa_id)->exists()) {
                throw ValidationException::withMessages([
                    'grupo' => 'El grupo no pertenece al programa académico asociado a tu perfil.',
                ]);
            }

            $existingEnrollment = Matricula::query()
                ->withTrashed()
                ->where('estudiante_id', $lockedStudent->id)
                ->where('grupo_id', $lockedGroup->id)
                ->lockForUpdate()
                ->first();

            if ($existingEnrollment !== null
                && ! $existingEnrollment->trashed()
                && $existingEnrollment->estado === 'activa') {
                throw ValidationException::withMessages([
                    'grupo' => 'Ya tienes una matrícula activa en este grupo.',
                ]);
            }

            $alreadyEnrolledInSubject = Matricula::query()
                ->where('estudiante_id', $lockedStudent->id)
                ->where('estado', 'activa')
                ->whereHas('grupo', function (Builder $query) use ($lockedGroup): void {
                    $query->where('periodo_id', $lockedGroup->periodo_id)
                        ->where('asignatura_id', $lockedGroup->asignatura_id);
                })
                ->exists();

            if ($alreadyEnrolledInSubject) {
                throw ValidationException::withMessages([
                    'grupo' => 'Ya tienes una matrícula activa en otro grupo de esta asignatura para el mismo periodo.',
                ]);
            }

            $activeEnrollments = Matricula::query()
                ->where('grupo_id', $lockedGroup->id)
                ->where('estado', 'activa')
                ->count();

            if ($activeEnrollments >= $lockedGroup->cupo) {
                throw ValidationException::withMessages([
                    'grupo' => 'El grupo ya no tiene cupos disponibles.',
                ]);
            }

            if ($existingEnrollment !== null) {
                if ($existingEnrollment->trashed()) {
                    $existingEnrollment->restore();
                }

                $existingEnrollment->update([
                    'estado' => 'activa',
                    'fecha' => $today->toDateString(),
                ]);

                return $existingEnrollment;
            }

            return $lockedStudent->matriculas()->create([
                'grupo_id' => $lockedGroup->id,
                'estado' => 'activa',
                'fecha' => $today->toDateString(),
            ]);
        }, attempts: 3);
    }
}
