@extends('layouts.app')

@php($isEditing = isset($periodo))

@section('title', $isEditing ? 'Editar periodo' : 'Crear periodo')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('staff.periodos.index') }}" class="text-sm font-semibold text-indigo-700">← Volver a periodos</a>
        <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-950">{{ $isEditing ? 'Editar periodo' : 'Crear periodo' }}</h1>
        <form method="POST" action="{{ $isEditing ? route('staff.periodos.update', $periodo) : route('staff.periodos.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre del periodo</label>
                <input id="nombre" name="nombre" value="{{ old('nombre', $periodo->nombre ?? '') }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('nombre')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="fecha_inicio" class="mb-2 block text-sm font-medium text-slate-700">Fecha de inicio</label>
                <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', isset($periodo) ? $periodo->fecha_inicio->toDateString() : '') }}" required class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('fecha_inicio')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="fecha_fin" class="mb-2 block text-sm font-medium text-slate-700">Fecha de finalización</label>
                <input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin', isset($periodo) ? $periodo->fecha_fin->toDateString() : '') }}" required class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                @error('fecha_fin')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                <input type="hidden" name="abierto" value="0">
                <input type="checkbox" name="abierto" value="1" @checked((bool) old('abierto', $periodo->abierto ?? false)) class="size-4 rounded border-slate-300 text-indigo-700 focus:ring-indigo-500">
                Habilitar matrículas para este periodo
            </label>
            @error('abierto')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('staff.periodos.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancelar</a>
                <button class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white">{{ $isEditing ? 'Guardar cambios' : 'Crear periodo' }}</button>
            </div>
        </form>
    </div>
@endsection
