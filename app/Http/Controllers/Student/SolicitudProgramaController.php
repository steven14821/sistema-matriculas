<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitudProgramaRequest;
use App\Models\Estudiante;
use App\Models\Programa;
use App\Models\SolicitudPrograma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SolicitudProgramaController extends Controller
{
    public function store(StoreSolicitudProgramaRequest $request): RedirectResponse
    {
        $student = $request->user()->estudiante;

        if ($student === null) {
            return back()->withErrors([
                'estudiante' => 'Tu usuario no tiene un perfil de estudiante asociado. Contacta a la administración.',
            ]);
        }

        if ($student->programa_id !== null) {
            return back()->withErrors([
                'programa_id' => 'Tu perfil ya tiene un programa asignado.',
            ]);
        }

        $validated = $request->validated();
        $receiptPath = $request->file('comprobante')->store('solicitudes-programa', 'local');

        if ($receiptPath === false) {
            throw ValidationException::withMessages([
                'comprobante' => 'No se pudo guardar el recibo. Intenta subirlo nuevamente.',
            ]);
        }

        $requestCreated = false;

        try {
            DB::transaction(function () use ($student, $validated, $receiptPath): void {
                $lockedStudent = Estudiante::query()
                    ->whereKey($student->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedStudent->programa_id !== null) {
                    throw ValidationException::withMessages([
                        'programa_id' => 'Tu perfil ya tiene un programa asignado.',
                    ]);
                }

                if ($lockedStudent->solicitudesPrograma()
                    ->where('estado', SolicitudPrograma::PENDIENTE)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'comprobante' => 'Ya tienes una solicitud pendiente de revisión.',
                    ]);
                }

                if (! Programa::query()->whereKey($validated['programa_id'])->exists()) {
                    throw ValidationException::withMessages([
                        'programa_id' => 'El programa seleccionado ya no está disponible.',
                    ]);
                }

                $lockedStudent->solicitudesPrograma()->create([
                    'programa_id' => $validated['programa_id'],
                    'comprobante_path' => $receiptPath,
                    'estado' => SolicitudPrograma::PENDIENTE,
                ]);
            });

            $requestCreated = true;
        } finally {
            if (! $requestCreated) {
                Storage::disk('local')->delete($receiptPath);
            }
        }

        return to_route('student.enrollments.index')
            ->with('status', 'Recibimos tu solicitud. El funcionario revisará el recibo antes de asignar el programa.');
    }
}
