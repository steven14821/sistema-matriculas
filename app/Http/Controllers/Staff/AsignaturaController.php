<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAsignaturaRequest;
use App\Models\Asignatura;
use App\Models\Programa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsignaturaController extends Controller
{
    public function index(): View
    {
        return view('staff.asignaturas.index', [
            'asignaturas' => Asignatura::query()
                ->with('programa')
                ->orderBy('codigo')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('staff.asignaturas.form', [
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(SaveAsignaturaRequest $request): RedirectResponse
    {
        Asignatura::query()->create($request->validated());

        return to_route('staff.asignaturas.index')->with('status', 'La asignatura fue creada correctamente.');
    }

    public function edit(Asignatura $asignatura): View
    {
        return view('staff.asignaturas.form', [
            'asignatura' => $asignatura,
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function update(SaveAsignaturaRequest $request, Asignatura $asignatura): RedirectResponse
    {
        $asignatura->update($request->validated());

        return to_route('staff.asignaturas.index')->with('status', 'La asignatura fue actualizada correctamente.');
    }

    public function destroy(Asignatura $asignatura): RedirectResponse
    {
        if ($asignatura->grupos()->withTrashed()->exists()) {
            return back()->withErrors([
                'asignatura' => 'No se puede eliminar una asignatura con grupos asociados.',
            ]);
        }

        $asignatura->delete();

        return to_route('staff.asignaturas.index')->with('status', 'La asignatura fue eliminada correctamente.');
    }
}
