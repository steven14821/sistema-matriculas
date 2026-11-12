<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfertaAcademicaController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'periodo_id' => ['nullable', 'exists:periodos,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $grupos = Grupo::query()
            ->with(['asignatura.programa', 'periodo', 'profesor'])
            ->withCount('matriculas')
            ->whereHas('periodo', fn ($query) => $query->where('abierto', true))
            ->when($validated['periodo_id'] ?? null, fn ($query, $periodoId) => $query->where('periodo_id', $periodoId))
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
            ->paginate(12)
            ->withQueryString();

        return view('oferta.index', [
            'grupos' => $grupos,
            'periodos' => Periodo::query()->where('abierto', true)->orderBy('nombre')->get(),
        ]);
    }
}
