@extends('layouts.app')

@section('title', 'Programas')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Programas</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Consulta y administra los programas académicos del campus.</p>
        </div>
        <a href="{{ route('admin.programas.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Crear programa
        </a>
    </div>

    <form method="GET" action="{{ route('admin.programas.index') }}" class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_auto] sm:items-end">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Buscar programa</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" maxlength="100" placeholder="Nombre o nivel" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Buscar</button>
            <a href="{{ route('admin.programas.index') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Limpiar</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Programa</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Nivel</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($programas as $programa)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $programa->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $programa->nivel }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.programas.edit', $programa) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Editar</a>
                                    <form method="POST" action="{{ route('admin.programas.destroy', $programa) }}" onsubmit="return confirm('¿Eliminar este programa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron programas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($programas->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $programas->links() }}</div>
        @endif
    </div>
@endsection
