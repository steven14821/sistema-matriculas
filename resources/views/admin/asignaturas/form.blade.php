@extends('layouts.app')

@section('title', isset($asignatura) ? 'Editar asignatura' : 'Crear asignatura')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ isset($asignatura) ? 'Editar asignatura' : 'Crear asignatura' }}</h1>
        </div>

        <form method="POST" action="{{ isset($asignatura) ? route('admin.asignaturas.update', $asignatura) : route('admin.asignaturas.store') }}" class="space-y-5">
            @csrf
            @if (isset($asignatura))
                @method('PUT')
            @endif

            <div>
                <label for="programa_id" class="mb-2 block text-sm font-medium text-slate-700">Programa</label>
                <select id="programa_id" name="programa_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm" required>
                    <option value="">Seleccione un programa</option>
                    @foreach ($programas as $programa)
                        <option value="{{ $programa->id }}" @selected(old('programa_id', $asignatura->programa_id ?? null) == $programa->id)>{{ $programa->nombre }}</option>
                    @endforeach
                </select>
                @error('programa_id')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="codigo" class="mb-2 block text-sm font-medium text-slate-700">Código</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $asignatura->codigo ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('codigo')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $asignatura->nombre ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('nombre')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="creditos" class="mb-2 block text-sm font-medium text-slate-700">Créditos</label>
                <input id="creditos" name="creditos" type="number" min="1" max="12" value="{{ old('creditos', $asignatura->creditos ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('creditos')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                <a href="{{ route('admin.asignaturas.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
