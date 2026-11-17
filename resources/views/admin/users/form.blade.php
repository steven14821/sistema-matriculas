@extends('layouts.app')

@section('title', 'Crear usuario')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Volver a usuarios</a>
        <div class="mt-5">
            <p class="text-sm font-semibold text-indigo-700">Administración</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Crear usuario</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Completa los datos, asigna un rol y guarda los datos académicos si crearás un estudiante.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nombre completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255" required autocomplete="name" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                @error('name')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" required autocomplete="email" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                @error('email')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="mb-2 block text-sm font-medium text-slate-700">Rol</label>
                <select id="role" name="role" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', \App\Enums\UserRole::Estudiante->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <fieldset class="space-y-5 rounded-xl border border-slate-200 p-5">
                <legend class="px-2 text-sm font-semibold text-slate-800">Perfil académico de estudiante</legend>
                <p class="text-sm leading-6 text-slate-600">Completa estos datos cuando el rol seleccionado sea Estudiante.</p>

                <div>
                    <label for="codigo" class="mb-2 block text-sm font-medium text-slate-700">Código estudiantil</label>
                    <input id="codigo" name="codigo" type="text" value="{{ old('codigo') }}" maxlength="255" autocomplete="off" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                    @error('codigo')
                        <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="documento" class="mb-2 block text-sm font-medium text-slate-700">Documento de identidad</label>
                    <input id="documento" name="documento" type="text" value="{{ old('documento') }}" maxlength="255" autocomplete="off" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                    @error('documento')
                        <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="programa_id" class="mb-2 block text-sm font-medium text-slate-700">Programa académico</label>
                    <select id="programa_id" name="programa_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                        <option value="">Selecciona un programa</option>
                        @foreach ($programas as $programa)
                            <option value="{{ $programa->id }}" @selected((string) old('programa_id', '') === (string) $programa->id)>{{ $programa->nombre }}</option>
                        @endforeach
                    </select>
                    @error('programa_id')
                        <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Contraseña</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                @error('password')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
                    Crear usuario
                </button>
            </div>
        </form>
    </div>
@endsection
