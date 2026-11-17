@extends('layouts.app')

@php($isEditing = isset($programa))

@section('title', $isEditing ? 'Editar programa' : 'Crear programa')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('staff.programas.index') }}" class="text-sm font-semibold text-indigo-700">← Volver a carreras</a>
        <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-950">{{ $isEditing ? 'Editar carrera' : 'Crear carrera' }}</h1>
        <form method="POST" action="{{ $isEditing ? route('staff.programas.update', $programa) : route('staff.programas.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif
            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="nombre" name="nombre" value="{{ old('nombre', $programa->nombre ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('nombre')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nivel" class="mb-2 block text-sm font-medium text-slate-700">Nivel académico</label>
                <input id="nivel" name="nivel" value="{{ old('nivel', $programa->nivel ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('nivel')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('staff.programas.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancelar</a>
                <button class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white">{{ $isEditing ? 'Guardar cambios' : 'Crear carrera' }}</button>
            </div>
        </form>
    </div>
@endsection
