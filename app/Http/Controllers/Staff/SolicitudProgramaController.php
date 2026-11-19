<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewSolicitudProgramaRequest;
use App\Models\Estudiante;
use App\Models\SolicitudPrograma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudProgramaController extends Controller
{
    public function index(): View
    {
        return view('staff.solicitudes-programa.index', [
            'solicitudes' => SolicitudPrograma::query()
                ->with(['estudiante.usuario', 'programa', 'revisor'])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15),
        ]);
    }

    public function receipt(SolicitudPrograma $solicitudPrograma): StreamedResponse
    {
        $storage = Storage::disk('local');

        abort_unless($storage->exists($solicitudPrograma->comprobante_path), 404);

        return $storage->download($solicitudPrograma->comprobante_path);
    }

    public function update(
        ReviewSolicitudProgramaRequest $request,
        SolicitudPrograma $solicitudPrograma,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $solicitudPrograma, $validated): void {
            $lockedRequest = SolicitudPrograma::query()
                ->whereKey($solicitudPrograma->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $lockedStudent = Estudiante::query()
                ->whereKey($lockedRequest->estudiante_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->estado !== SolicitudPrograma::PENDIENTE) {
                throw ValidationException::withMessages([
                    'estado' => 'Esta solicitud ya fue revisada.',
                ]);
            }

            if ($validated['estado'] === SolicitudPrograma::APROBADA) {
                if ($lockedStudent->programa_id !== null
                    && $lockedStudent->programa_id !== $lockedRequest->programa_id) {
                    throw ValidationException::withMessages([
                        'estado' => 'El estudiante ya tiene asignado otro programa. No se puede aprobar esta solicitud.',
                    ]);
                }

                $lockedStudent->update(['programa_id' => $lockedRequest->programa_id]);
            }

            $lockedRequest->update([
                'estado' => $validated['estado'],
                'observacion' => $validated['observacion'] ?? null,
                'revisado_por_user_id' => $request->user()->id,
                'revisado_en' => now(),
            ]);
        }, attempts: 3);

        return to_route('staff.solicitudes_programa.index')
            ->with('status', 'La solicitud fue actualizada correctamente.');
    }
}
