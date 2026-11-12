@extends('layouts.app')

@section('title', $isStaff ? 'Matrículas' : 'Mi matrícula')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ $isStaff ? 'Matrículas' : 'Mi matrícula' }}</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $isStaff ? 'Consulta, filtra y administra las matrículas del sistema.' : 'Revisa los grupos en los que te encuentras inscrito.' }}</p>
        </div>
        @if ($isStaff)
            <a href="{{ route('admin.matriculas.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
                Registrar matrícula
            </a>
        @endif
    </div>

    @if ($isStaff)
        <form method="GET" action="{{ route('admin.matriculas.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4 md:items-end">
            <div>
                <label for="periodo_id" class="mb-2 block text-sm font-medium text-slate-700">Periodo</label>
                <select id="periodo_id" name="periodo_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Todos</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}" @selected(request('periodo_id') == $periodo->id)>{{ $periodo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estudiante_id" class="mb-2 block text-sm font-medium text-slate-700">Estudiante</label>
                <select id="estudiante_id" name="estudiante_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Todos</option>
                    @foreach ($estudiantes as $estudiante)
                        <option value="{{ $estudiante->id }}" @selected(request('estudiante_id') == $estudiante->id)>{{ $estudiante->usuario->name }} ({{ $estudiante->codigo }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado" class="mb-2 block text-sm font-medium text-slate-700">Estado</label>
                <select id="estado" name="estado" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Todos</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Filtrar</button>
                <a href="{{ route('admin.matriculas.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
            </div>
        </form>
    @endif

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        @if ($isStaff)
                            <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estudiante</th>
                        @endif
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Asignatura</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Grupo</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($matriculas as $matricula)
                        <tr>
                            @if ($isStaff)
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <span class="block font-semibold text-slate-900">{{ $matricula->estudiante->usuario->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $matricula->estudiante->codigo }}</span>
                                </td>
                            @endif
                            <td class="px-5 py-4 text-sm text-slate-700">
                                <span class="block font-semibold text-slate-900">{{ $matricula->grupo?->asignatura?->nombre }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $matricula->grupo?->asignatura?->codigo }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $matricula->grupo?->periodo?->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">Grupo {{ $matricula->grupo?->codigo }}</td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $matricula->estado === 'activa',
                                    'bg-amber-50 text-amber-800' => $matricula->estado === 'pendiente',
                                ])>{{ ucfirst($matricula->estado) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end">
                                    <form method="POST" action="{{ route('matriculas.destroy', $matricula) }}" onsubmit="return confirm('¿Cancelar esta matrícula?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">{{ $isStaff ? 'Eliminar' : 'Cancelar' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isStaff ? 6 : 5 }}" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron matrículas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
