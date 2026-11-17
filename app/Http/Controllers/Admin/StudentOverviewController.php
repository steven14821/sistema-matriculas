<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentOverviewController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $estudiantes = Estudiante::query()
            ->with(['usuario', 'programa'])
            ->withCount(['matriculas' => fn (Builder $query): Builder => $query->where('estado', 'activa')])
            ->when($validated['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('codigo', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%")
                        ->orWhereHas('usuario', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('programa', fn (Builder $query): Builder => $query->where('nombre', 'like', "%{$search}%"));
                });
            })
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return view('admin.estudiantes.index', compact('estudiantes'));
    }

    public function show(Estudiante $estudiante): View
    {
        $estudiante->load([
            'usuario',
            'programa.asignaturas',
            'matriculas' => fn (Relation $query) => $query
                ->withTrashed()
                ->with([
                    'grupo' => fn (Relation $query) => $query
                        ->withTrashed()
                        ->with([
                            'asignatura' => fn (Relation $query) => $query->withTrashed()->with('programa'),
                            'periodo' => fn (Relation $query) => $query->withTrashed(),
                        ]),
                ])
                ->orderByDesc('fecha')
                ->orderByDesc('id'),
        ]);

        return view('admin.estudiantes.show', compact('estudiante'));
    }
}
