@extends('layouts.app')

@section('title', 'Registrar matrícula')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Registrar matrícula manual</h1>
        </div>

        <form method="POST" action="{{ route('admin.matriculas.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="estudiante_id" class="mb-2 block text-sm font-medium text-slate-700">Estudiante</label>
                <select id="estudiante_id" name="estudiante_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                    <option value="">Seleccione un estudiante</option>
                    @foreach ($estudiantes as $estudiante)
                        <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>{{ $estudiante->usuario->name }} ({{ $estudiante->codigo }})</option>
                    @endforeach
                </select>
                @error('estudiante_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="grupo_id" class="mb-2 block text-sm font-medium text-slate-700">Grupo</label>
                <select id="grupo_id" name="grupo_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                    <option value="">Seleccione un grupo</option>
                    @foreach ($grupos as $grupo)
                        <option value="{{ $grupo->id }}" @selected(old('grupo_id') == $grupo->id)>{{ $grupo->asignatura->nombre }} · Grupo {{ $grupo->codigo }} · {{ $grupo->periodo->nombre }} · {{ $grupo->profesor?->nombre ?? 'Profesor pendiente' }}</option>
                    @endforeach
                </select>
                @error('grupo_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="estado" class="mb-2 block text-sm font-medium text-slate-700">Estado</label>
                    <select id="estado" name="estado" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                        <option value="activa" @selected(old('estado') === 'activa')>Activa</option>
                        <option value="pendiente" @selected(old('estado') === 'pendiente')>Pendiente</option>
                    </select>
                    @error('estado')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="mb-2 block text-sm font-medium text-slate-700">Fecha</label>
                    <input id="fecha" name="fecha" type="date" value="{{ old('fecha', now()->toDateString()) }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                    @error('fecha')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                <a href="{{ route('admin.matriculas.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
