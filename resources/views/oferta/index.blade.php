@extends('layouts.app')

@section('title', 'Oferta académica')

@section('content')
    <header class="flex flex-col justify-between gap-5 border-b border-slate-300 pb-6 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Grupos disponibles</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                Compara asignaturas, profesores y cupos abiertos antes de elegir tus grupos.
            </p>
        </div>
        <a href="{{ auth()->user()->role->value === 'estudiante' ? route('matriculas.mine') : route('admin.matriculas.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">
            {{ auth()->user()->role->value === 'estudiante' ? 'Ver mi matrícula' : 'Consultar matrículas' }} <span aria-hidden="true">→</span>
        </a>
    </header>

    <section class="mt-7 border-b border-slate-300 pb-7" aria-label="Filtrar oferta académica">
        <form method="GET" action="{{ route('oferta.index') }}" class="grid gap-4 sm:grid-cols-[minmax(150px,0.75fr)_minmax(220px,1.25fr)_auto] sm:items-end">
            <div>
                <label for="periodo_id" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">Periodo abierto</label>
                <select id="periodo_id" name="periodo_id" class="block w-full border border-slate-300 bg-white px-3 py-3 text-sm">
                    <option value="">Todos los periodos</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}" @selected(request('periodo_id') == $periodo->id)>{{ $periodo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">Buscar en la oferta</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Asignatura, código o profesor" class="block w-full border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-indigo-700">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Filtrar</button>
                <a href="{{ route('oferta.index') }}" class="border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">Limpiar</a>
            </div>
        </form>
    </section>

    <section class="mt-8" aria-labelledby="available-groups-title">
        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-baseline">
            <h2 id="available-groups-title" class="text-2xl font-medium text-slate-950">Oferta por grupo</h2>
            <p class="text-sm text-slate-600">{{ $grupos->total() }} {{ $grupos->total() === 1 ? 'grupo disponible' : 'grupos disponibles' }}</p>
        </div>

        <div class="mt-4 overflow-x-auto border-y border-slate-300 bg-white">
            <table class="academic-table min-w-[790px]">
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-4">Asignatura</th>
                        <th scope="col" class="px-4 py-4">Grupo / periodo</th>
                        <th scope="col" class="px-4 py-4">Profesor</th>
                        <th scope="col" class="px-4 py-4">Cupos</th>
                        <th scope="col" class="px-4 py-4">Créditos</th>
                        <th scope="col" class="px-4 py-4"><span class="sr-only">Acción</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grupos as $grupo)
                        @php
                            $disponibles = max(0, $grupo->cupo - $grupo->matriculas_count);
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <span class="block text-sm font-semibold text-slate-950">{{ $grupo->asignatura?->nombre }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $grupo->asignatura?->codigo }} · {{ $grupo->asignatura?->programa?->nombre }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-sm font-medium text-slate-800">Grupo {{ $grupo->codigo }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $grupo->periodo?->nombre }}</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $grupo->profesor?->nombre ?? 'Pendiente de asignación' }}</td>
                            <td class="px-4 py-4">
                                <span @class([
                                    'text-sm font-semibold tabular-nums',
                                    'text-emerald-700' => $disponibles > 0,
                                    'text-red-700' => $disponibles === 0,
                                ])>{{ $disponibles }} de {{ $grupo->cupo }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $disponibles > 0 ? 'disponibles' : 'sin cupo' }}</span>
                            </td>
                            <td class="px-4 py-4 text-sm tabular-nums text-slate-700">{{ $grupo->asignatura?->creditos }}</td>
                            <td class="px-4 py-4 text-right">
                                @if (auth()->user()->role->value === 'estudiante')
                                    <form method="POST" action="{{ route('matriculas.inscribir', $grupo) }}">
                                        @csrf
                                        <button type="submit" @disabled($disponibles === 0) class="whitespace-nowrap border border-indigo-700 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-700 hover:text-white disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400 disabled:hover:bg-transparent disabled:hover:text-slate-400">
                                            {{ $disponibles > 0 ? 'Inscribirme' : 'Sin cupo' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Oferta abierta</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="font-serif text-xl text-slate-900">No hay grupos para mostrar</p>
                                <p class="mt-2 text-sm text-slate-500">Prueba con otros filtros o vuelve a consultar más adelante.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($grupos->hasPages())
            <div class="mt-5">{{ $grupos->links() }}</div>
        @endif
    </section>
@endsection
