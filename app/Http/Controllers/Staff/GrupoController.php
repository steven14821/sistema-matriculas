<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveGrupoRequest;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(): View
    {
        return view('staff.grupos.index', [
            'grupos' => Grupo::query()
                ->with(['asignatura', 'periodo'])
                ->orderBy('periodo_id')
                ->orderBy('codigo')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('staff.grupos.form', [
            'asignaturas' => Asignatura::query()->orderBy('nombre')->get(),
            'periodos' => Periodo::query()->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function store(SaveGrupoRequest $request): RedirectResponse
    {
        Grupo::query()->create($request->validated());

        return to_route('staff.grupos.index')->with('status', 'El grupo fue creado correctamente.');
    }

    public function edit(Grupo $grupo): View
    {
        return view('staff.grupos.form', [
            'grupo' => $grupo,
            'asignaturas' => Asignatura::query()->orderBy('nombre')->get(),
            'periodos' => Periodo::query()->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function update(SaveGrupoRequest $request, Grupo $grupo): RedirectResponse
    {
        $grupo->update($request->validated());

        return to_route('staff.grupos.index')->with('status', 'El grupo fue actualizado correctamente.');
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        if ($grupo->matriculas()->withTrashed()->exists()) {
            return back()->withErrors([
                'grupo' => 'No se puede eliminar un grupo con matrículas asociadas.',
            ]);
        }

        $grupo->delete();

        return to_route('staff.grupos.index')->with('status', 'El grupo fue eliminado correctamente.');
    }
}
