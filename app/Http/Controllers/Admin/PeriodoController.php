<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodoController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $periodos = Periodo::query()
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.periodos.index', compact('periodos'));
    }

    public function create(): View
    {
        return view('admin.periodos.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:periodos,nombre'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'abierto' => ['nullable', 'boolean'],
        ]);

        Periodo::query()->create($validated);

        return to_route('admin.periodos.index')->with('status', 'El periodo académico fue creado correctamente.');
    }

    public function edit(Periodo $periodo): View
    {
        return view('admin.periodos.form', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:periodos,nombre,'.$periodo->id],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'abierto' => ['nullable', 'boolean'],
        ]);

        $periodo->update($validated);

        return to_route('admin.periodos.index')->with('status', 'El periodo académico fue actualizado correctamente.');
    }

    public function destroy(Periodo $periodo): RedirectResponse
    {
        $periodo->delete();

        return to_route('admin.periodos.index')->with('status', 'El periodo académico fue eliminado correctamente.');
    }
}
