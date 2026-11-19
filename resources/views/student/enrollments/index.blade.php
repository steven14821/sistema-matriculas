@extends('layouts.app')

@section('title', 'Oferta y matrícula')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Tu espacio académico</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Oferta y matrícula</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Consulta los grupos de tu programa y matricúlate mientras el periodo esté abierto y haya cupos. Si aún no tienes programa, puedes solicitarlo aquí.
        </p>
    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($student === null)
        <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm leading-6 text-amber-900" role="status">
            Tu usuario no tiene un perfil de estudiante asociado. Contacta a la administración para completar tu registro.
        </div>
    @elseif ($student->programa_id === null)
        <section class="mt-9 max-w-3xl" aria-labelledby="program-request-title">
            @if ($programRequest?->estado === 'pendiente')
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6" role="status">
                    <h2 id="program-request-title" class="text-xl font-semibold text-amber-950">Solicitud en revisión</h2>
                    <p class="mt-2 text-sm leading-6 text-amber-900">
                        Elegiste {{ $programRequest->programa->nombre }}. Un funcionario debe revisar tu recibo antes de asignar el programa y habilitar las materias.
                    </p>
                    <p class="mt-3 text-xs font-medium text-amber-800">Enviada el {{ $programRequest->created_at->format('d/m/Y') }}</p>
                </div>
            @else
                @if ($programRequest?->estado === 'rechazada')
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5" role="status">
                        <h2 class="text-lg font-semibold text-red-950">El recibo necesita una corrección</h2>
                        <p class="mt-2 text-sm leading-6 text-red-900">{{ $programRequest->observacion }}</p>
                        <p class="mt-2 text-sm text-red-800">Puedes enviar una nueva solicitud con el recibo corregido.</p>
                    </div>
                @endif

                @if ($programas->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm leading-6 text-slate-600">
                        No hay programas disponibles para solicitar en este momento. Contacta a la administración.
                    </div>
                @else
                    <form method="POST" action="{{ route('student.solicitudes_programa.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        @csrf
                        <div>
                            <h2 id="program-request-title" class="text-xl font-semibold text-slate-950">Solicita tu programa</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Elige el programa y adjunta el recibo de pago. El funcionario revisará el comprobante; las materias aparecerán cuando apruebe la solicitud.
                            </p>
                        </div>

                        <div>
                            <label for="programa_id" class="mb-2 block text-sm font-medium text-slate-700">Programa académico</label>
                            <select id="programa_id" name="programa_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                                <option value="">Selecciona un programa</option>
                                @foreach ($programas as $programa)
                                    <option value="{{ $programa->id }}" @selected((string) old('programa_id') === (string) $programa->id)>{{ $programa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('programa_id')
                                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="comprobante" class="mb-2 block text-sm font-medium text-slate-700">Recibo de pago</label>
                            <input id="comprobante" name="comprobante" type="file" accept=".pdf,.jpg,.jpeg,.png" required aria-describedby="comprobante-help" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                            <p id="comprobante-help" class="mt-2 text-xs leading-5 text-slate-500">PDF, JPG o PNG; máximo 5 MB. Solo el funcionario podrá consultar el archivo.</p>
                            @error('comprobante')
                                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end border-t border-slate-100 pt-5">
                            <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                                Enviar solicitud
                            </button>
                        </div>
                    </form>
                @endif
            @endif
        </section>
    @else
        <p class="mt-5 text-sm font-medium text-slate-700">
            Programa asignado: <span class="font-semibold text-slate-950">{{ $student->programa->nombre }}</span>
        </p>

        <section id="mis-matriculas" class="mt-9">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-slate-950">Mis matrículas activas</h2>
                <p class="mt-1 text-sm text-slate-600">Grupos en los que ya tienes una inscripción activa.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura</th>
                                <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Créditos</th>
                                <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                                <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Horario</th>
                                <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($enrollments as $enrollment)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="block text-sm font-semibold text-slate-900">{{ $enrollment->grupo->asignatura->nombre }}</span>
                                        <span class="mt-1 block text-sm text-slate-500">{{ $enrollment->grupo->asignatura->codigo }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->asignatura->creditos }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->periodo->nombre }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->horario ?: 'Por definir' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center">
                                        <p class="text-sm font-semibold text-slate-900">Aún no tienes matrículas activas</p>
                                        <p class="mt-1 text-sm text-slate-500">Revisa la oferta disponible para inscribirte.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="oferta" class="mt-10">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-slate-950">Oferta de grupos</h2>
                <p class="mt-1 text-sm text-slate-600">Solo se muestran grupos de {{ $student->programa->nombre }} en periodos abiertos.</p>
            </div>

            @if ($openPeriods->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm leading-6 text-slate-600">
                    En este momento no hay periodos habilitados para matrícula.
                </div>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($openPeriods as $period)
                        @php
                            $periodCredits = $enrolledCreditsByPeriod->get($period->id, 0);
                        @endphp
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                            <h3 class="text-sm font-semibold text-slate-900">{{ $period->nombre }}</h3>
                            @if ($period->limite_creditos === null)
                                <p class="mt-1 text-sm text-amber-800">Límite pendiente de configuración</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">El funcionario debe definirlo para habilitar matrículas.</p>
                            @else
                                <p class="mt-1 text-sm text-slate-600">{{ $periodCredits }} de {{ $period->limite_creditos }} créditos matriculados</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($offeredGroups->isEmpty())
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 text-sm leading-6 text-slate-600">
                        No hay grupos disponibles para tu programa en los periodos abiertos.
                    </div>
                @else
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura / grupo</th>
                                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Docente y horario</th>
                                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Cupos</th>
                                    <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($offeredGroups as $group)
                                    @php
                                        $availableSeats = max(0, $group->cupo - $group->matriculas_activas_count);
                                        $subjectPeriodKey = $group->periodo_id.':'.$group->asignatura_id;
                                        $currentCredits = $enrolledCreditsByPeriod->get($group->periodo_id, 0);
                                        $creditLimit = $group->periodo->limite_creditos;
                                    @endphp
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="block text-sm font-semibold text-slate-900">{{ $group->asignatura->nombre }}</span>
                                            <span class="mt-1 block text-sm text-slate-500">{{ $group->asignatura->codigo }}</span>
                                            <span class="mt-1 block text-xs text-slate-500">{{ $group->asignatura->creditos }} créditos</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $group->periodo->nombre }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-700">
                                            <span class="block">{{ $group->profesor?->nombre ?? $group->docente ?? 'Docente por definir' }}</span>
                                            <span class="mt-1 block text-slate-500">{{ $group->horario ?: 'Horario por definir' }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                            {{ $availableSeats }} de {{ $group->cupo }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            @if (in_array($group->id, $enrolledGroupIds, true))
                                                <span class="text-sm font-semibold text-emerald-700">Inscrito</span>
                                            @elseif (in_array($subjectPeriodKey, $enrolledSubjectPeriodKeys, true))
                                                <span class="text-sm font-medium text-slate-500">Ya cursas esta asignatura</span>
                                            @elseif ($creditLimit === null)
                                                <span class="text-sm font-medium text-amber-800">Límite pendiente de configuración</span>
                                            @elseif ($currentCredits + $group->asignatura->creditos > $creditLimit)
                                                <span class="text-sm font-medium text-slate-500">Excede el límite del periodo</span>
                                            @elseif ($availableSeats === 0)
                                                <span class="text-sm font-medium text-slate-500">Sin cupos</span>
                                            @else
                                                <form method="POST" action="{{ route('student.enrollments.store', $group) }}">
                                                    @csrf
                                                    <button type="submit" class="rounded-xl bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
                                                        Matricularme
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif
        </section>
    @endif
@endsection
