<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\Models\Programa;
use Illuminate\View\View;

class AcademicOverviewController extends Controller
{
    public function index(): View
    {
        return view('admin.catalogo.index', [
            'programas' => Programa::query()
                ->with(['asignaturas' => fn ($query) => $query->orderBy('codigo')])
                ->withCount('estudiantes')
                ->orderBy('nombre')
                ->get(),
            'periodos' => Periodo::query()
                ->with(['grupos' => fn ($query) => $query->with(['asignatura', 'profesor'])->orderBy('codigo')])
                ->orderByDesc('fecha_inicio')
                ->get(),
        ]);
    }
}
