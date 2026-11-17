@extends('layouts.app')

@section('title', 'Estudiantes y matrículas')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Consulta general</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Estudiantes y matrículas</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Busca un estudiante y abre su ficha para consultar carrera, asignaturas inscritas y el historial de matrícula.</p>
    </div>

    <form method="GET" action="{{ route('admin.estudiantes.index') }}" class="mt-8 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        <div class="flex-1">
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar estudiante</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Nombre, correo, código, documento o carrera" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-700">Buscar</button>
            <a href="{{ route('admin.estudiantes.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estudiante</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Carrera</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Matrículas activas</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($estudiantes as $estudiante)
                        <tr>
                            <td class="px-5 py-4">
                                <span class="block text-sm font-semibold text-slate-900">{{ $estudiante->usuario->name }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $estudiante->usuario->email }}</span>
                                <span class="mt-1 block text-xs text-slate-500">Código {{ $estudiante->codigo }} · Documento {{ $estudiante->documento }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $estudiante->programa->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $estudiante->matriculas_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.estudiantes.show', $estudiante) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Ver ficha</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron estudiantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($estudiantes->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $estudiantes->links() }}</div>
        @endif
    </div>
@endsection
