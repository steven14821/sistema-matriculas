@extends('layouts.app')

@section('title', 'Grupos')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Grupos</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Identifica cada grupo por código y asigna sus profesores.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.profesores.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Catálogo de profesores
            </a>
            @can('manage-academic-data')
                <a href="{{ route('admin.grupos.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
                    Crear grupo
                </a>
            @endcan
        </div>
    </div>

    <form method="GET" action="{{ route('admin.grupos.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_auto] sm:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Código, profesor o asignatura" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Buscar</button>
            <a href="{{ route('admin.grupos.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Grupo</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Profesor</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Cupo</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($grupos as $grupo)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $grupo->codigo }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">
                                <span class="block font-semibold text-slate-900">{{ $grupo->asignatura?->nombre }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $grupo->asignatura?->codigo }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $grupo->periodo?->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $grupo->profesor?->nombre ?? 'Pendiente de asignación' }}</td>
                            <td class="px-5 py-4 text-sm tabular-nums text-slate-600">{{ $grupo->matriculas_count }} / {{ $grupo->cupo }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col items-end gap-3">
                                    <form method="POST" action="{{ route('admin.grupos.assign-professor', $grupo) }}" class="flex w-full min-w-64 max-w-sm gap-2">
                                        @csrf
                                        @method('PUT')
                                        <label for="profesor-{{ $grupo->id }}" class="sr-only">Profesor del grupo {{ $grupo->codigo }}</label>
                                        <select id="profesor-{{ $grupo->id }}" name="profesor_id" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                                            <option value="">Sin asignar</option>
                                            @foreach ($profesores as $profesor)
                                                <option value="{{ $profesor->id }}" @selected($grupo->profesor_id == $profesor->id)>{{ $profesor->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-lg bg-indigo-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                                    </form>
                                    @can('manage-academic-data')
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.grupos.edit', $grupo) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Editar grupo</a>
                                            <form method="POST" action="{{ route('admin.grupos.destroy', $grupo) }}" onsubmit="return confirm('¿Eliminar este grupo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron grupos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($grupos->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $grupos->links() }}</div>
        @endif
    </div>
@endsection
