@extends('layouts.app')

@section('title', 'Periodos académicos')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Periodos académicos</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Define los ciclos disponibles para matrícula y cupos.</p>
        </div>
        <a href="{{ route('admin.periodos.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Crear periodo
        </a>
    </div>

    <form method="GET" action="{{ route('admin.periodos.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_auto] sm:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar periodo</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Nombre del periodo" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Buscar</button>
            <a href="{{ route('admin.periodos.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha inicio</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha fin</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($periodos as $periodo)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $periodo->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $periodo->fecha_inicio?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $periodo->fecha_fin?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $periodo->abierto,
                                    'bg-slate-100 text-slate-700' => ! $periodo->abierto,
                                ])>
                                    {{ $periodo->abierto ? 'Abierto' : 'Cerrado' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.periodos.edit', $periodo) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Editar</a>
                                    <form method="POST" action="{{ route('admin.periodos.destroy', $periodo) }}" onsubmit="return confirm('¿Eliminar este periodo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron periodos académicos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($periodos->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $periodos->links() }}</div>
        @endif
    </div>
@endsection
