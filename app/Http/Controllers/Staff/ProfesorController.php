<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfesorController extends Controller
{
    public function index(): View
    {
        return view('staff.profesores.index', [
            'profesores' => Profesor::query()->withCount('grupos')->orderBy('nombre')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('staff.profesores.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:profesores,nombre'],
        ]);

        Profesor::query()->create($validated);

        return to_route('staff.profesores.index')->with('status', 'El profesor fue agregado al catálogo.');
    }

    public function edit(Profesor $profesor): View
    {
        return view('staff.profesores.form', [
            'profesor' => $profesor,
        ]);
    }

    public function update(Request $request, Profesor $profesor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('profesores', 'nombre')->ignore($profesor),
            ],
        ]);

        $profesor->update($validated);

        return to_route('staff.profesores.index')->with('status', 'El nombre del profesor fue actualizado.');
    }

    public function destroy(Profesor $profesor): RedirectResponse
    {
        if (Grupo::withTrashed()->where('profesor_id', $profesor->id)->exists()) {
            return back()->withErrors([
                'profesor' => 'No puedes eliminar un profesor asignado a uno o más grupos.',
            ]);
        }

        $profesor->delete();

        return to_route('staff.profesores.index')->with('status', 'El profesor fue eliminado del catálogo.');
    }
}
