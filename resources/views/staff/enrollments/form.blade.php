@extends('layouts.app')

@section('title', 'Registrar matrícula')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('staff.enrollments.index') }}" class="text-sm font-semibold text-indigo-700">← Volver a matrículas</a>
        <p class="mt-5 text-sm font-semibold text-indigo-700">Gestión académica</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Registrar matrícula</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">El estudiante y el grupo deben pertenecer al mismo programa y el periodo debe estar abierto.</p>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.enrollments.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <div>
                <label for="estudiante_id" class="mb-2 block text-sm font-medium text-slate-700">Estudiante</label>
                <select id="estudiante_id" name="estudiante_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Selecciona un estudiante</option>
                    @foreach ($estudiantes as $estudiante)
                        <option value="{{ $estudiante->id }}" @selected((string) old('estudiante_id') === (string) $estudiante->id)>{{ $estudiante->codigo }} · {{ $estudiante->usuario->name }} ({{ $estudiante->programa?->nombre ?? 'Programa pendiente' }})</option>
                    @endforeach
                </select>
                @error('estudiante_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="grupo_id" class="mb-2 block text-sm font-medium text-slate-700">Grupo del periodo abierto</label>
                <select id="grupo_id" name="grupo_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                    <option value="">Selecciona un grupo</option>
                    @foreach ($grupos as $grupo)
                        <option value="{{ $grupo->id }}" @selected((string) old('grupo_id') === (string) $grupo->id)>{{ $grupo->periodo->nombre }} · {{ $grupo->codigo }} · {{ $grupo->asignatura->nombre }}</option>
                    @endforeach
                </select>
                @error('grupo_id')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            @if ($estudiantes->isEmpty() || $grupos->isEmpty())
                <p class="rounded-xl bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                    @if ($estudiantes->isEmpty()) No hay estudiantes con programa asignado. @endif
                    @if ($grupos->isEmpty()) No hay grupos en periodos actualmente abiertos. @endif
                </p>
            @endif
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('staff.enrollments.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancelar</a>
                <button @disabled($estudiantes->isEmpty() || $grupos->isEmpty()) class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">Registrar matrícula</button>
            </div>
        </form>
    </div>
@endsection
