@extends('layouts.app')

@section('title', isset($periodo) ? 'Editar periodo' : 'Crear periodo')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ isset($periodo) ? 'Editar periodo' : 'Crear periodo' }}</h1>
        </div>

        <form method="POST" action="{{ isset($periodo) ? route('admin.periodos.update', $periodo) : route('admin.periodos.store') }}" class="space-y-5">
            @csrf
            @if (isset($periodo))
                @method('PUT')
            @endif

            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $periodo->nombre ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('nombre')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="fecha_inicio" class="mb-2 block text-sm font-medium text-slate-700">Fecha inicio</label>
                    <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', isset($periodo) ? $periodo->fecha_inicio?->format('Y-m-d') : '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                    @error('fecha_inicio')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_fin" class="mb-2 block text-sm font-medium text-slate-700">Fecha fin</label>
                    <input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin', isset($periodo) ? $periodo->fecha_fin?->format('Y-m-d') : '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                    @error('fecha_fin')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <label for="abierto" class="flex items-center gap-3 text-sm text-slate-700">
                <input id="abierto" name="abierto" type="checkbox" value="1" @checked(old('abierto', $periodo->abierto ?? false)) class="size-4 rounded border-slate-300 text-indigo-700">
                Periodo abierto para matrícula
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                <a href="{{ route('admin.periodos.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
