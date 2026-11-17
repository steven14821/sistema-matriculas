<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePeriodoRequest;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PeriodoController extends Controller
{
    public function index(): View
    {
        return view('staff.periodos.index', [
            'periodos' => Periodo::query()->orderByDesc('fecha_inicio')->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('staff.periodos.form');
    }

    public function store(SavePeriodoRequest $request): RedirectResponse
    {
        Periodo::query()->create($request->validated());

        return to_route('staff.periodos.index')->with('status', 'El periodo fue creado correctamente.');
    }

    public function edit(Periodo $periodo): View
    {
        return view('staff.periodos.form', ['periodo' => $periodo]);
    }

    public function update(SavePeriodoRequest $request, Periodo $periodo): RedirectResponse
    {
        $periodo->update($request->validated());

        return to_route('staff.periodos.index')->with('status', 'El periodo fue actualizado correctamente.');
    }

    public function destroy(Periodo $periodo): RedirectResponse
    {
        if ($periodo->grupos()->withTrashed()->exists()) {
            return back()->withErrors([
                'periodo' => 'No se puede eliminar un periodo con grupos asociados.',
            ]);
        }

        $periodo->delete();

        return to_route('staff.periodos.index')->with('status', 'El periodo fue eliminado correctamente.');
    }
}
