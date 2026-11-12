@extends('layouts.app')

@section('title', isset($grupo) ? 'Editar grupo' : 'Crear grupo')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ isset($grupo) ? 'Editar grupo' : 'Crear grupo' }}</h1>
        </div>

        <form method="POST" action="{{ isset($grupo) ? route('admin.grupos.update', $grupo) : route('admin.grupos.store') }}" class="space-y-5">
            @csrf
            @if (isset($grupo))
                @method('PUT')
            @endif

            <div>
                <label for="codigo" class="mb-2 block text-sm font-medium text-slate-700">Código del grupo</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $grupo->codigo ?? '') }}" maxlength="50" placeholder="Ej. A101" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                <p class="mt-2 text-sm text-slate-500">El mismo código puede repetirse en otra asignatura o periodo.</p>
                @error('codigo')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="asignatura_id" class="mb-2 block text-sm font-medium text-slate-700">Asignatura</label>
                <select id="asignatura_id" name="asignatura_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                    <option value="">Seleccione una asignatura</option>
                    @foreach ($asignaturas as $asignatura)
                        <option value="{{ $asignatura->id }}" @selected(old('asignatura_id', $grupo->asignatura_id ?? null) == $asignatura->id)>{{ $asignatura->nombre }} ({{ $asignatura->codigo }})</option>
                    @endforeach
                </select>
                @error('asignatura_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="periodo_id" class="mb-2 block text-sm font-medium text-slate-700">Periodo</label>
                <select id="periodo_id" name="periodo_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                    <option value="">Seleccione un periodo</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}" @selected(old('periodo_id', $grupo->periodo_id ?? null) == $periodo->id)>{{ $periodo->nombre }}</option>
                    @endforeach
                </select>
                @error('periodo_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profesor_id" class="mb-2 block text-sm font-medium text-slate-700">Profesor asignado</label>
                <select id="profesor_id" name="profesor_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Pendiente de asignación</option>
                    @foreach ($profesores as $profesor)
                        <option value="{{ $profesor->id }}" @selected(old('profesor_id', $grupo->profesor_id ?? null) == $profesor->id)>{{ $profesor->nombre }}</option>
                    @endforeach
                </select>
                @error('profesor_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cupo" class="mb-2 block text-sm font-medium text-slate-700">Cupo</label>
                <input id="cupo" name="cupo" type="number" min="1" value="{{ old('cupo', $grupo->cupo ?? 30) }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('cupo')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                <a href="{{ route('admin.grupos.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
