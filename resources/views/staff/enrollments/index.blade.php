@extends('layouts.app')

@section('title', 'Gestión de matrículas')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Gestión académica</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Matrículas</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Consulta, filtra y administra las matrículas de los estudiantes.</p>
        </div>
        <a href="{{ route('staff.enrollments.create') }}" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-800">Registrar matrícula</a>
    </div>

    @error('matricula')
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
    @enderror

    <form method="GET" action="{{ route('staff.enrollments.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_14rem_12rem_auto] md:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar estudiante</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Nombre, correo, código o documento" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
        </div>
        <div>
            <label for="periodo_id" class="mb-2 block text-sm font-medium text-slate-700">Periodo</label>
            <select id="periodo_id" name="periodo_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                <option value="">Todos los periodos</option>
                @foreach ($periodos as $periodo)
                    <option value="{{ $periodo->id }}" @selected((string) request('periodo_id') === (string) $periodo->id)>{{ $periodo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado" class="mb-2 block text-sm font-medium text-slate-700">Estado</label>
            <select id="estado" name="estado" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                <option value="">Todos</option>
                <option value="activa" @selected(request('estado') === 'activa')>Activa</option>
                <option value="cancelada" @selected(request('estado') === 'cancelada')>Cancelada</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Filtrar</button>
            <a href="{{ route('staff.enrollments.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50"><tr>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estudiante</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura / grupo</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha / estado</th>
                    <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td class="px-5 py-4">
                                <span class="block text-sm font-semibold text-slate-900">{{ $enrollment->estudiante->usuario->name }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $enrollment->estudiante->codigo }} · {{ $enrollment->estudiante->documento }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="block text-sm font-semibold text-slate-900">{{ $enrollment->grupo->asignatura->nombre }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $enrollment->grupo->codigo }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $enrollment->grupo->periodo->nombre }}</td>
                            <td class="px-5 py-4">
                                <span class="block text-sm text-slate-700">{{ $enrollment->fecha->format('d/m/Y') }}</span>
                                <span @class([
                                    'mt-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $enrollment->estado === 'activa',
                                    'bg-slate-100 text-slate-700' => $enrollment->estado === 'cancelada',
                                ])>{{ ucfirst($enrollment->estado) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($enrollment->estado === 'activa')
                                    <form method="POST" action="{{ route('staff.enrollments.destroy', $enrollment) }}" onsubmit="return confirm('¿Cancelar esta matrícula?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm font-semibold text-red-700">Cancelar</button>
                                    </form>
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No se encontraron matrículas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($enrollments->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $enrollments->links() }}</div>@endif
    </div>
@endsection
