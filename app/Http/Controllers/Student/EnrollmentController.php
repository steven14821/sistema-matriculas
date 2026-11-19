<?php

namespace App\Http\Controllers\Student;

use App\Actions\EnrollStudentInGroup;
use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Programa;
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
        $programas = collect();
        $programRequest = null;

        if ($student !== null) {
            if ($student->programa_id !== null) {
                $offeredGroups = Grupo::query()
                    ->whereIn('periodo_id', $openPeriods->modelKeys())
                    ->whereHas('asignatura', fn (Builder $query): Builder => $query->where('programa_id', $student->programa_id))
                    ->with(['asignatura', 'periodo', 'profesor'])
                    ->withCount([
                        'matriculas as matriculas_activas_count' => fn (Builder $query): Builder => $query->where('estado', 'activa'),
                    ])
                    ->orderBy('periodo_id')
                    ->orderBy('asignatura_id')
                    ->orderBy('id')
                    ->get();
            } else {
                $programRequest = $student->solicitudesPrograma()
                    ->with('programa')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->first();

                if ($programRequest?->estado !== 'pendiente') {
                    $programas = Programa::query()->orderBy('nombre')->get();
                }
            }

            $enrollments = Matricula::query()
                ->where('estudiante_id', $student->id)
                ->where('estado', 'activa')
                ->with(['grupo.asignatura', 'grupo.periodo', 'grupo.profesor'])
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->get();
        }

        $enrolledCreditsByPeriod = $enrollments
            ->groupBy(fn (Matricula $enrollment): int => $enrollment->grupo->periodo_id)
            ->map(fn ($periodEnrollments): int => $periodEnrollments
                ->sum(fn (Matricula $enrollment): int => $enrollment->grupo->asignatura->creditos));

        $enrolledGroupIds = $enrollments->pluck('grupo_id')->all();
        $enrolledSubjectPeriodKeys = $enrollments
            ->map(fn (Matricula $enrollment): string => $enrollment->grupo->periodo_id.':'.$enrollment->grupo->asignatura_id)
            ->all();

        return view('student.enrollments.index', [
            'student' => $student,
            'openPeriods' => $openPeriods,
            'offeredGroups' => $offeredGroups,
            'enrollments' => $enrollments,
            'programas' => $programas,
            'programRequest' => $programRequest,
            'enrolledCreditsByPeriod' => $enrolledCreditsByPeriod,
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

        if ($student->programa_id === null) {
            return back()->withErrors([
                'estudiante' => 'Debes tener un programa aprobado antes de matricular asignaturas.',
            ]);
        }

        $enrollStudentInGroup->handle($student, $grupo);

        return to_route('student.enrollments.index')
            ->with('status', 'La matrícula se realizó correctamente.');
    }

    public function destroy(Request $request, Matricula $matricula): RedirectResponse
    {
        $student = $request->user()->estudiante;

        abort_unless($student !== null && $matricula->estudiante_id === $student->id, 403);

        if ($matricula->estado !== 'activa') {
            return back()->withErrors([
                'matricula' => 'Solo puedes cancelar una matrícula activa.',
            ]);
        }

        $matricula->update(['estado' => 'cancelada']);

        return to_route('matriculas.mine')->with('status', 'La matrícula fue cancelada correctamente.');
    }
}
