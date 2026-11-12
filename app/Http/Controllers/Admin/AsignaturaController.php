<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use App\Models\Programa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsignaturaController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $asignaturas = Asignatura::query()
            ->with('programa')
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.asignaturas.index', [
            'asignaturas' => $asignaturas,
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.asignaturas.form', [
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:asignaturas,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        Asignatura::query()->create($validated);

        return to_route('admin.asignaturas.index')->with('status', 'La asignatura fue creada correctamente.');
    }

    public function edit(Asignatura $asignatura): View
    {
        return view('admin.asignaturas.form', [
            'asignatura' => $asignatura,
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Asignatura $asignatura): RedirectResponse
    {
        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:asignaturas,codigo,'.$asignatura->id],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $asignatura->update($validated);

        return to_route('admin.asignaturas.index')->with('status', 'La asignatura fue actualizada correctamente.');
    }

    public function destroy(Asignatura $asignatura): RedirectResponse
    {
        $asignatura->delete();

        return to_route('admin.asignaturas.index')->with('status', 'La asignatura fue eliminada correctamente.');
    }
}
