<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Profesor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $grupos = Grupo::query()
            ->with(['asignatura.programa', 'periodo', 'profesor'])
            ->withCount('matriculas')
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('codigo', 'like', "%{$search}%")
                        ->orWhereHas('profesor', fn ($profesorQuery) => $profesorQuery->where('nombre', 'like', "%{$search}%"))
                        ->orWhereHas('asignatura', function ($asignaturaQuery) use ($search): void {
                            $asignaturaQuery->where('nombre', 'like', "%{$search}%")
                                ->orWhere('codigo', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.grupos.index', [
            'grupos' => $grupos,
            'profesores' => Profesor::query()->orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.grupos.form', [
            'asignaturas' => Asignatura::query()->with('programa')->orderBy('nombre')->get(),
            'periodos' => Periodo::query()->orderBy('fecha_inicio', 'desc')->get(),
            'profesores' => Profesor::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request));

        Grupo::query()->create($validated);

        return to_route('admin.grupos.index')->with('status', 'El grupo fue creado correctamente.');
    }

    public function edit(Grupo $grupo): View
    {
        return view('admin.grupos.form', [
            'grupo' => $grupo,
            'asignaturas' => Asignatura::query()->with('programa')->orderBy('nombre')->get(),
            'periodos' => Periodo::query()->orderBy('fecha_inicio', 'desc')->get(),
            'profesores' => Profesor::query()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Grupo $grupo): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, $grupo));

        $grupo->update($validated);

        return to_route('admin.grupos.index')->with('status', 'El grupo fue actualizado correctamente.');
    }

    public function assignProfessor(Request $request, Grupo $grupo): RedirectResponse
    {
        $validated = $request->validate([
            'profesor_id' => ['nullable', 'exists:profesores,id'],
        ]);

        $grupo->update(['profesor_id' => $validated['profesor_id'] ?? null]);

        return to_route('admin.grupos.index')->with('status', 'El profesor del grupo fue actualizado correctamente.');
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        $grupo->delete();

        return to_route('admin.grupos.index')->with('status', 'El grupo fue eliminado correctamente.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(Request $request, ?Grupo $grupo = null): array
    {
        $codigoRule = Rule::unique('grupos', 'codigo')
            ->where('asignatura_id', $request->input('asignatura_id'))
            ->where('periodo_id', $request->input('periodo_id'));

        if ($grupo !== null) {
            $codigoRule->ignore($grupo);
        }

        return [
            'codigo' => ['required', 'string', 'max:50', $codigoRule],
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'profesor_id' => ['nullable', 'exists:profesores,id'],
            'cupo' => ['required', 'integer', 'min:1'],
        ];
    }
}
