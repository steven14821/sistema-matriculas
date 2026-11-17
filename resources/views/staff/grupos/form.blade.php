@extends('layouts.app')

@php($isEditing = isset($grupo))

@section('title', $isEditing ? 'Editar grupo' : 'Crear grupo')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('staff.grupos.index') }}" class="text-sm font-semibold text-indigo-700">← Volver a grupos</a>
        <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-950">{{ $isEditing ? 'Editar grupo' : 'Crear grupo' }}</h1>
        <form method="POST" action="{{ $isEditing ? route('staff.grupos.update', $grupo) : route('staff.grupos.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif
            <div>
                <label for="codigo" class="mb-2 block text-sm font-medium text-slate-700">Código del grupo</label>
                <input id="codigo" name="codigo" value="{{ old('codigo', $grupo->codigo ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('codigo')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="asignatura_id" class="mb-2 block text-sm font-medium text-slate-700">Asignatura</label>
                <select id="asignatura_id" name="asignatura_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Selecciona una asignatura</option>
                    @foreach ($asignaturas as $asignatura)
                        <option value="{{ $asignatura->id }}" @selected((string) old('asignatura_id', $grupo->asignatura_id ?? '') === (string) $asignatura->id)>{{ $asignatura->codigo }} · {{ $asignatura->nombre }}</option>
                    @endforeach
                </select>
                @error('asignatura_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="periodo_id" class="mb-2 block text-sm font-medium text-slate-700">Periodo</label>
                <select id="periodo_id" name="periodo_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Selecciona un periodo</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}" @selected((string) old('periodo_id', $grupo->periodo_id ?? '') === (string) $periodo->id)>{{ $periodo->nombre }}</option>
                    @endforeach
                </select>
                @error('periodo_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="profesor_id" class="mb-2 block text-sm font-medium text-slate-700">Profesor asignado</label>
                <select id="profesor_id" name="profesor_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Pendiente de asignación</option>
                    @foreach ($profesores as $profesor)
                        <option value="{{ $profesor->id }}" @selected((string) old('profesor_id', $grupo->profesor_id ?? '') === (string) $profesor->id)>{{ $profesor->nombre }}</option>
                    @endforeach
                </select>
                @error('profesor_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="horario" class="mb-2 block text-sm font-medium text-slate-700">Horario</label>
                <input id="horario" name="horario" value="{{ old('horario', $grupo->horario ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('horario')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="cupo" class="mb-2 block text-sm font-medium text-slate-700">Cupo máximo</label>
                <input id="cupo" name="cupo" type="number" min="1" value="{{ old('cupo', $grupo->cupo ?? '') }}" required class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('cupo')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('staff.grupos.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancelar</a>
                <button class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white">{{ $isEditing ? 'Guardar cambios' : 'Crear grupo' }}</button>
            </div>
        </form>
    </div>
@endsection
