@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Administración</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Usuarios</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Crea cuentas y administra los perfiles de acceso al sistema.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Crear usuario
        </a>
    </div>

    @error('user')
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
    @enderror

    <form method="GET" action="{{ route('admin.users.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_14rem_auto] sm:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar usuario</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Nombre o correo electrónico" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
        </div>
        <div>
            <label for="role" class="mb-2 block text-sm font-medium text-slate-700">Filtrar por rol</label>
            <select id="role" name="role" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
                <option value="">Todos los roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Buscar</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Rol</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Creado</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="block text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $user->email }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-violet-50 text-violet-700' => $user->role === \App\Enums\UserRole::Administrador,
                                    'bg-cyan-50 text-cyan-800' => $user->role === \App\Enums\UserRole::Funcionario,
                                    'bg-emerald-50 text-emerald-700' => $user->role === \App\Enums\UserRole::Estudiante,
                                ])>{{ $user->role->label() }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Editar</a>
                                    @if (! $user->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="text-sm font-semibold text-slate-900">No se encontraron usuarios</p>
                                <p class="mt-1 text-sm text-slate-500">Prueba con otros filtros o crea una cuenta nueva.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
