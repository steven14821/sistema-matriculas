<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MatriculaController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'periodo_id' => ['nullable', 'exists:periodos,id'],
            'estudiante_id' => ['nullable', 'exists:estudiantes,id'],
            'estado' => ['nullable', Rule::in(['activa', 'pendiente'])],
        ]);

        $matriculas = Matricula::query()
            ->with(['estudiante.usuario', 'grupo.asignatura.programa', 'grupo.periodo', 'grupo.profesor'])
            ->when($validated['periodo_id'] ?? null, fn ($query, $periodoId) => $query->whereHas('grupo', fn ($groupQuery) => $groupQuery->where('periodo_id', $periodoId)))
            ->when($validated['estudiante_id'] ?? null, fn ($query, $estudianteId) => $query->where('estudiante_id', $estudianteId))
            ->when($validated['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('matriculas.index', [
            'matriculas' => $matriculas,
            'periodos' => Periodo::query()->orderBy('nombre')->get(),
            'estudiantes' => Estudiante::query()->with('usuario')->orderBy('codigo')->get(),
            'estados' => ['activa', 'pendiente'],
            'isStaff' => true,
        ]);
    }

    public function create(): View
    {
        $grupos = Grupo::query()
            ->with(['asignatura.programa', 'periodo', 'profesor'])
            ->withCount('matriculas')
            ->whereHas('periodo', fn ($query) => $query->where('abierto', true))
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Grupo $grupo): bool => $grupo->matriculas_count < $grupo->cupo);

        return view('matriculas.form', [
            'estudiantes' => Estudiante::query()->with('usuario')->orderBy('codigo')->get(),
            'grupos' => $grupos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'grupo_id' => ['required', 'exists:grupos,id'],
            'estado' => ['required', Rule::in(['activa', 'pendiente'])],
            'fecha' => ['required', 'date'],
        ]);

        $estudiante = Estudiante::query()->findOrFail($validated['estudiante_id']);
        $grupo = Grupo::query()->with('periodo')->findOrFail($validated['grupo_id']);

        if (! $grupo->periodo?->abierto) {
            return back()
                ->withErrors(['grupo_id' => 'Solo puedes matricular en grupos de periodos abiertos.'])
                ->withInput();
        }

        $result = $this->registerEnrollment(
            $estudiante,
            $grupo->id,
            $validated['estado'],
            $validated['fecha'],
        );

        if ($result === 'duplicate') {
            return back()->withErrors(['grupo_id' => 'El estudiante ya está matriculado en este grupo.'])->withInput();
        }

        if ($result === 'capacity') {
            return back()->withErrors(['grupo_id' => 'Este grupo ya alcanzó su cupo máximo.'])->withInput();
        }

        return to_route('admin.matriculas.index')->with('status', 'La matrícula fue registrada correctamente.');
    }

    public function mine(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->role === UserRole::Estudiante && $user->estudiante, 403);

        $matriculas = Matricula::query()
            ->with(['grupo.asignatura.programa', 'grupo.periodo', 'grupo.profesor'])
            ->where('estudiante_id', $user->estudiante->id)
            ->latest('id')
            ->get();

        return view('matriculas.index', [
            'matriculas' => $matriculas,
            'periodos' => Periodo::query()->orderBy('nombre')->get(),
            'estudiantes' => collect(),
            'estados' => ['activa', 'pendiente'],
            'isStaff' => false,
        ]);
    }

    public function inscribir(Request $request, Grupo $grupo): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->role === UserRole::Estudiante && $user->estudiante, 403);

        if (! $grupo->periodo || ! $grupo->periodo->abierto) {
            return back()->withErrors(['grupo' => 'El periodo académico del grupo no está abierto.'])->withInput();
        }

        $result = $this->registerEnrollment(
            $user->estudiante,
            $grupo->id,
            'activa',
            now()->toDateString(),
        );

        if ($result === 'duplicate') {
            return back()->withErrors(['grupo' => 'Ya te encuentras matriculado en este grupo.'])->withInput();
        }

        if ($result === 'capacity') {
            return back()->withErrors(['grupo' => 'Este grupo ya no tiene cupo disponible.'])->withInput();
        }

        return redirect()->route('matriculas.mine')->with('status', 'Tu matrícula fue registrada correctamente.');
    }

    public function destroy(Request $request, Matricula $matricula): RedirectResponse
    {
        $user = $request->user();

        if ($user->role === UserRole::Estudiante) {
            abort_unless($matricula->estudiante_id === $user->estudiante?->id, 403);

            $matricula->delete();

            return redirect()->route('matriculas.mine')->with('status', 'La matrícula fue cancelada correctamente.');
        }

        Gate::authorize('manage-matriculas');
        $matricula->delete();

        return to_route('admin.matriculas.index')->with('status', 'La matrícula fue eliminada correctamente.');
    }

    private function registerEnrollment(
        Estudiante $estudiante,
        int $grupoId,
        string $estado,
        string $fecha,
    ): ?string {
        return DB::transaction(function () use ($estudiante, $grupoId, $estado, $fecha): ?string {
            $grupo = Grupo::query()->lockForUpdate()->findOrFail($grupoId);
            $matricula = Matricula::withTrashed()
                ->where('estudiante_id', $estudiante->id)
                ->where('grupo_id', $grupo->id)
                ->first();

            if ($matricula !== null && ! $matricula->trashed()) {
                return 'duplicate';
            }

            if ($grupo->matriculas()->count() >= $grupo->cupo) {
                return 'capacity';
            }

            if ($matricula !== null) {
                $matricula->fill([
                    'estado' => $estado,
                    'fecha' => $fecha,
                ]);
                $matricula->restore();

                return null;
            }

            Matricula::query()->create([
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'estado' => $estado,
                'fecha' => $fecha,
            ]);

            return null;
        });
    }
}
