@extends('layouts.app')

@section('title', 'Catálogo académico')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Consulta general</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Catálogo académico</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Vista de solo lectura de las carreras, sus asignaturas y los grupos disponibles por periodo.</p>
    </div>

    <section class="mt-8" aria-labelledby="programas-heading">
        <h2 id="programas-heading" class="text-xl font-semibold text-slate-950">Carreras y asignaturas</h2>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            @forelse ($programas as $programa)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950">{{ $programa->nombre }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $programa->nivel }}</p>
                        </div>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $programa->estudiantes_count }} {{ $programa->estudiantes_count === 1 ? 'estudiante' : 'estudiantes' }}</span>
                    </div>
                    <h4 class="mt-5 text-sm font-semibold text-slate-800">Asignaturas ({{ $programa->asignaturas->count() }})</h4>
                    <ul class="mt-2 divide-y divide-slate-100">
                        @forelse ($programa->asignaturas as $asignatura)
                            <li class="flex flex-wrap justify-between gap-2 py-3 text-sm">
                                <span><span class="font-semibold text-slate-800">{{ $asignatura->codigo }}</span> · {{ $asignatura->nombre }}</span>
                                <span class="text-slate-500">{{ $asignatura->creditos }} créditos</span>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-slate-500">Esta carrera aún no tiene asignaturas.</li>
                        @endforelse
                    </ul>
                </article>
            @empty
                <p class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600">Aún no hay carreras registradas.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-10" aria-labelledby="periodos-heading">
        <h2 id="periodos-heading" class="text-xl font-semibold text-slate-950">Periodos y grupos</h2>
        <div class="mt-4 space-y-4">
            @forelse ($periodos as $periodo)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-950">{{ $periodo->nombre }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $periodo->fecha_inicio->format('d/m/Y') }} – {{ $periodo->fecha_fin->format('d/m/Y') }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-emerald-50 text-emerald-700' => $periodo->abierto,
                            'bg-slate-100 text-slate-600' => ! $periodo->abierto,
                        ])>{{ $periodo->abierto ? 'Matrícula abierta' : 'Matrícula cerrada' }}</span>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Grupo y asignatura</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Profesor / horario</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Cupo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($periodo->grupos as $grupo)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="block font-semibold text-slate-900">{{ $grupo->codigo }}</span>
                                            <span class="text-slate-600">{{ $grupo->asignatura->nombre }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">
                                            <span class="block">{{ $grupo->profesor?->nombre ?? $grupo->docente ?? 'Pendiente de asignación' }}</span>
                                            <span class="text-slate-500">{{ $grupo->horario }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $grupo->cupo }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-4 text-sm text-slate-500">Este periodo aún no tiene grupos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600">Aún no hay periodos registrados.</p>
            @endforelse
        </div>
    </section>
@endsection
