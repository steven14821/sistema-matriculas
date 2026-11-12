<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramaController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $programas = Programa::query()
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('nivel', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.programas.index', compact('programas'));
    }

    public function create(): View
    {
        return view('admin.programas.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:programas,nombre'],
            'nivel' => ['required', 'string', 'max:255'],
        ]);

        Programa::query()->create($validated);

        return to_route('admin.programas.index')->with('status', 'El programa fue creado correctamente.');
    }

    public function edit(Programa $programa): View
    {
        return view('admin.programas.form', compact('programa'));
    }

    public function update(Request $request, Programa $programa): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:programas,nombre,'.$programa->id],
            'nivel' => ['required', 'string', 'max:255'],
        ]);

        $programa->update($validated);

        return to_route('admin.programas.index')->with('status', 'El programa fue actualizado correctamente.');
    }

    public function destroy(Programa $programa): RedirectResponse
    {
        $programa->delete();

        return to_route('admin.programas.index')->with('status', 'El programa fue eliminado correctamente.');
    }
}
