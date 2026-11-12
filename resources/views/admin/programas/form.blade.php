@extends('layouts.app')

@section('title', isset($programa) ? 'Editar programa' : 'Crear programa')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ isset($programa) ? 'Editar programa' : 'Crear programa' }}</h1>
        </div>

        <form method="POST" action="{{ isset($programa) ? route('admin.programas.update', $programa) : route('admin.programas.store') }}" class="space-y-5">
            @csrf
            @if (isset($programa))
                @method('PUT')
            @endif

            <div>
                <label for="nombre" class="mb-2 block text-sm font-medium text-slate-700">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $programa->nombre ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('nombre')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nivel" class="mb-2 block text-sm font-medium text-slate-700">Nivel</label>
                <input id="nivel" name="nivel" type="text" value="{{ old('nivel', $programa->nivel ?? '') }}" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" required>
                @error('nivel')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">Guardar</button>
                <a href="{{ route('admin.programas.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
