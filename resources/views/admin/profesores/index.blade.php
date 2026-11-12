@extends('layouts.app')

@section('title', 'Profesores')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Catálogo de profesores</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Mantén la lista de profesores disponible para asignar a los grupos.</p>
        </div>
        <a href="{{ route('admin.profesores.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Agregar profesor
        </a>
    </div>

    @if ($errors->has('profesor'))
        <p class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $errors->first('profesor') }}</p>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Profesor</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Grupos asignados</th>
                        <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($profesores as $profesor)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $profesor->nombre }}</td>
                            <td class="px-5 py-4 text-sm tabular-nums text-slate-600">{{ $profesor->grupos_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('admin.profesores.edit', $profesor) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Editar</a>
                                    <form method="POST" action="{{ route('admin.profesores.destroy', $profesor) }}" onsubmit="return confirm('¿Eliminar este profesor del catálogo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-sm text-slate-500">Todavía no hay profesores en el catálogo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($profesores->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $profesores->links() }}</div>
        @endif
    </div>
@endsection
