@extends('layouts.app')

@section('title', 'Ficha del estudiante')

@section('content')
    <a href="{{ route('admin.estudiantes.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Volver a estudiantes</a>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
            <div>
                <p class="text-sm font-semibold text-indigo-700">Ficha del estudiante</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $estudiante->usuario->name }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $estudiante->usuario->email }}</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                @php($activeEnrollments = $estudiante->matriculas->where('estado', 'activa')->count())
                {{ $activeEnrollments }} {{ $activeEnrollments === 1 ? 'matrícula activa' : 'matrículas activas' }}
            </span>
        </div>
        <dl class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-3">
            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Código</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $estudiante->codigo }}</dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Documento</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $estudiante->documento }}</dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Carrera</dt><dd class="mt-1 text-sm font-medium text-slate-900">{{ $estudiante->programa->nombre }}</dd></div>
        </dl>
    </div>

    <section class="mt-9" aria-labelledby="enrollments-heading">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 id="enrollments-heading" class="text-xl font-semibold text-slate-950">Asignaturas matriculadas</h2>
                <p class="mt-1 text-sm text-slate-600">Incluye matrículas activas y registros cancelados.</p>
            </div>
        </div>
        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura / grupo</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Docente / horario</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($estudiante->matriculas as $matricula)
                            <tr>
                                <td class="px-5 py-4">
                                    <span class="block text-sm font-semibold text-slate-900">{{ $matricula->grupo?->asignatura?->nombre ?? 'Asignatura no disponible' }}</span>
                                    <span class="mt-1 block text-sm text-slate-500">{{ $matricula->grupo?->asignatura?->codigo }} · Grupo {{ $matricula->grupo?->codigo }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700">{{ $matricula->grupo?->periodo?->nombre ?? 'Periodo no disponible' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <span class="block">{{ $matricula->grupo?->docente }}</span>
                                    <span class="text-slate-500">{{ $matricula->grupo?->horario }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $matricula->fecha->format('d/m/Y') }}</td>
                                <td class="px-5 py-4">
                                    <span @class([
                                        'rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-emerald-50 text-emerald-700' => $matricula->estado === 'activa',
                                        'bg-slate-100 text-slate-600' => $matricula->estado !== 'activa',
                                    ])>{{ ucfirst($matricula->estado) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Este estudiante aún no tiene matrículas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mt-9" aria-labelledby="career-subjects-heading">
        <h2 id="career-subjects-heading" class="text-xl font-semibold text-slate-950">Asignaturas de su carrera</h2>
        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <ul class="divide-y divide-slate-100">
                @forelse ($estudiante->programa->asignaturas as $asignatura)
                    <li class="flex flex-wrap justify-between gap-2 py-3 text-sm">
                        <span><span class="font-semibold text-slate-800">{{ $asignatura->codigo }}</span> · {{ $asignatura->nombre }}</span>
                        <span class="text-slate-500">{{ $asignatura->creditos }} créditos</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-slate-500">La carrera aún no tiene asignaturas registradas.</li>
                @endforelse
            </ul>
        </div>
    </section>
@endsection
