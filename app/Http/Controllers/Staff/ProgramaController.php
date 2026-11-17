<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProgramaRequest;
use App\Models\Programa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramaController extends Controller
{
    public function index(): View
    {
        return view('staff.programas.index', [
            'programas' => Programa::query()->withCount('asignaturas')->orderBy('nombre')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('staff.programas.form');
    }

    public function store(SaveProgramaRequest $request): RedirectResponse
    {
        Programa::query()->create($request->validated());

        return to_route('staff.programas.index')->with('status', 'El programa fue creado correctamente.');
    }

    public function edit(Programa $programa): View
    {
        return view('staff.programas.form', ['programa' => $programa]);
    }

    public function update(SaveProgramaRequest $request, Programa $programa): RedirectResponse
    {
        $programa->update($request->validated());

        return to_route('staff.programas.index')->with('status', 'El programa fue actualizado correctamente.');
    }

    public function destroy(Programa $programa): RedirectResponse
    {
        if ($programa->asignaturas()->withTrashed()->exists()
            || $programa->estudiantes()->withTrashed()->exists()) {
            return back()->withErrors([
                'programa' => 'No se puede eliminar un programa con asignaturas o estudiantes asociados.',
            ]);
        }

        $programa->delete();

        return to_route('staff.programas.index')->with('status', 'El programa fue eliminado correctamente.');
    }
}
