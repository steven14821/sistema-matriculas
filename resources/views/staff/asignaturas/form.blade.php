@extends('layouts.app')

@php($isEditing = isset($asignatura))

@section('title', $isEditing ? 'Editar asignatura' : 'Crear asignatura')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('staff.asignaturas.index') }}" class="text-sm font-semibold text-indigo-700">← Volver a asignaturas</a>
        <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-950">{{ $isEditing ? 'Editar asignatura' : 'Crear asignatura' }}</h1>
        <form method="POST" action="{{ $isEditing ? route('staff.asignaturas.update', $asignatura) : route('staff.asignaturas.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div>
                <label for="programa_id" class="mb-2 block text-sm font-medium text-slate-700">Programa</label>
                <select id="programa_id" name="programa_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Selecciona un programa</option>
                    @foreach ($programas as $programa)
                        <option value="{{ $programa->id }}" @selected((string) old('programa_id', $asignatura->programa_id ?? '') === (string) $programa->id)>{{ $programa->nombre }}</option>
                    @endforeach
                </select>
                @error('programa_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="codigo" class="mb-2 block text-sm font-medium text-slate-700">Código</label>
                <input id="codigo" name="codigo" value="{{ old('codigo', $asignatura->codigo ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('codigo')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="nombre" name="nombre" value="{{ old('nombre', $asignatura->nombre ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('nombre')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="creditos" class="mb-2 block text-sm font-medium text-slate-700">Créditos</label>
                <input id="creditos" name="creditos" type="number" min="1" max="255" value="{{ old('creditos', $asignatura->creditos ?? '') }}" required class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('creditos')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('staff.asignaturas.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancelar</a>
                <button class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white">{{ $isEditing ? 'Guardar cambios' : 'Crear asignatura' }}</button>
            </div>
        </form>
    </div>
@endsection
