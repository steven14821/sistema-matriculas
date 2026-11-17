<?php

namespace App\Http\Controllers\Staff;

use App\Actions\EnrollStudentInGroup;
use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'periodo_id' => ['nullable', 'integer', Rule::exists('periodos', 'id')->whereNull('deleted_at')],
            'estado' => ['nullable', Rule::in(['activa', 'cancelada'])],
        ]);

        $enrollments = Matricula::query()
            ->with(['estudiante.usuario', 'grupo.asignatura', 'grupo.periodo'])
            ->when($validated['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('estudiante', function (Builder $studentQuery) use ($search): void {
                    $studentQuery->where('codigo', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%")
                        ->orWhereHas('usuario', function (Builder $userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($validated['periodo_id'] ?? null, function (Builder $query, int $periodId): void {
                $query->whereHas('grupo', fn (Builder $groupQuery): Builder => $groupQuery->where('periodo_id', $periodId));
            })
            ->when($validated['estado'] ?? null, fn (Builder $query, string $state): Builder => $query->where('estado', $state))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('staff.enrollments.index', [
            'enrollments' => $enrollments,
            'periodos' => Periodo::query()->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function create(): View
    {
        $today = today()->toDateString();

        return view('staff.enrollments.form', [
            'estudiantes' => Estudiante::query()->with('usuario', 'programa')->orderBy('codigo')->get(),
            'grupos' => Grupo::query()
                ->whereHas('periodo', function (Builder $query) use ($today): void {
                    $query->where('abierto', true)
                        ->whereDate('fecha_inicio', '<=', $today)
                        ->whereDate('fecha_fin', '>=', $today);
                })
                ->with(['asignatura', 'periodo'])
                ->orderBy('periodo_id')
                ->orderBy('asignatura_id')
                ->get(),
        ]);
    }

    public function store(Request $request, EnrollStudentInGroup $enrollStudentInGroup): RedirectResponse
    {
        $validated = $request->validate([
            'estudiante_id' => ['required', 'integer', Rule::exists('estudiantes', 'id')->whereNull('deleted_at')],
            'grupo_id' => ['required', 'integer', Rule::exists('grupos', 'id')->whereNull('deleted_at')],
        ]);

        $student = Estudiante::query()->findOrFail($validated['estudiante_id']);
        $group = Grupo::query()->findOrFail($validated['grupo_id']);
        $enrollStudentInGroup->handle($student, $group);

        return to_route('staff.enrollments.index')->with('status', 'La matrícula fue registrada correctamente.');
    }

    public function destroy(Matricula $enrollment): RedirectResponse
    {
        $cancelled = DB::transaction(function () use ($enrollment): bool {
            $group = Grupo::query()
                ->whereKey($enrollment->grupo_id)
                ->lockForUpdate()
                ->firstOrFail();
            Estudiante::query()
                ->whereKey($enrollment->estudiante_id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedEnrollment = Matricula::query()
                ->whereKey($enrollment->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedEnrollment->estado !== 'activa') {
                return false;
            }

            $lockedEnrollment->update(['estado' => 'cancelada']);

            return true;
        }, attempts: 3);

        if (! $cancelled) {
            return back()->withErrors(['matricula' => 'La matrícula ya no está activa.']);
        }

        return to_route('staff.enrollments.index')->with('status', 'La matrícula fue cancelada correctamente.');
    }
}
