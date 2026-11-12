@extends('layouts.app')

@section('title', isset($profesor) ? 'Editar profesor' : 'Agregar profesor')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ isset($profesor) ? 'Editar profesor' : 'Agregar profesor' }}</h1>
        </div>

        <form method="POST" action="{{ isset($profesor) ? route('admin.profesores.update', $profesor) : route('admin.profesores.store') }}" class="space-y-5">
            @csrf
            @if (isset($profesor))
                @method('PUT')
            @endif

            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre completo</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $profesor->nombre ?? '') }}" maxlength="255" autocomplete="name" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('nombre')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar profesor</button>
                <a href="{{ route('admin.profesores.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
