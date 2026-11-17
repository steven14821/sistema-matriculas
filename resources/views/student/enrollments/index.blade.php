@extends('layouts.app')

@section('title', 'Oferta y matrícula')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Tu espacio académico</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Oferta y matrícula</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Consulta los grupos de tu programa y matricúlate mientras el periodo esté abierto y haya cupos.
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
    @else
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
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->periodo->nombre }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->horario ?: 'Por definir' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center">
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
                <p class="mt-1 text-sm text-slate-600">Solo se muestran grupos del programa asociado a tu perfil.</p>
            </div>

            @if ($openPeriods->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm leading-6 text-slate-600">
                    En este momento no hay periodos habilitados para matrícula.
                </div>
            @elseif ($offeredGroups->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm leading-6 text-slate-600">
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
                                    @endphp
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="block text-sm font-semibold text-slate-900">{{ $group->asignatura->nombre }}</span>
                                            <span class="mt-1 block text-sm text-slate-500">{{ $group->asignatura->codigo }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $group->periodo->nombre }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-700">
                                            <span class="block">{{ $group->docente ?: 'Docente por definir' }}</span>
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
        </section>
    @endif
@endsection
