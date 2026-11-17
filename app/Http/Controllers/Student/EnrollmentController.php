<?php

namespace App\Http\Controllers\Student;

use App\Actions\EnrollStudentInGroup;
use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->estudiante;
        $today = today()->toDateString();
        $openPeriods = Periodo::query()
            ->where('abierto', true)
            ->whereDate('fecha_inicio', '<=', $today)
            ->whereDate('fecha_fin', '>=', $today)
            ->orderBy('fecha_inicio')
            ->orderBy('id')
            ->get();

        $offeredGroups = collect();
        $enrollments = collect();

        if ($student !== null) {
            $offeredGroups = Grupo::query()
                ->whereIn('periodo_id', $openPeriods->modelKeys())
                ->whereHas('asignatura', fn (Builder $query): Builder => $query->where('programa_id', $student->programa_id))
                ->with(['asignatura', 'periodo'])
                ->withCount([
                    'matriculas as matriculas_activas_count' => fn (Builder $query): Builder => $query->where('estado', 'activa'),
                ])
                ->orderBy('periodo_id')
                ->orderBy('asignatura_id')
                ->orderBy('id')
                ->get();

            $enrollments = Matricula::query()
                ->where('estudiante_id', $student->id)
                ->where('estado', 'activa')
                ->with(['grupo.asignatura', 'grupo.periodo'])
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->get();
        }

        $enrolledGroupIds = $enrollments->pluck('grupo_id')->all();
        $enrolledSubjectPeriodKeys = $enrollments
            ->map(fn (Matricula $enrollment): string => $enrollment->grupo->periodo_id.':'.$enrollment->grupo->asignatura_id)
            ->all();

        return view('student.enrollments.index', [
            'student' => $student,
            'openPeriods' => $openPeriods,
            'offeredGroups' => $offeredGroups,
            'enrollments' => $enrollments,
            'enrolledGroupIds' => $enrolledGroupIds,
            'enrolledSubjectPeriodKeys' => $enrolledSubjectPeriodKeys,
        ]);
    }

    public function store(Request $request, Grupo $grupo, EnrollStudentInGroup $enrollStudentInGroup): RedirectResponse
    {
        $student = $request->user()->estudiante;

        if ($student === null) {
            return back()->withErrors([
                'estudiante' => 'Tu usuario no tiene un perfil de estudiante asociado. Contacta a la administración.',
            ]);
        }

        $enrollStudentInGroup->handle($student, $grupo);

        return to_route('student.enrollments.index')
            ->with('status', 'La matrícula se realizó correctamente.');
    }
}
