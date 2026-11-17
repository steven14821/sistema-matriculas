@extends('layouts.app')

@section('title', 'Grupos')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Gestión académica</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Grupos</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Administra la oferta de grupos, profesores, horarios y cupos.</p>
        </div>
        <a href="{{ route('staff.grupos.create') }}" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-800">Crear grupo</a>
    </div>
    @error('grupo')<div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>@enderror
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="academic-table min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50"><tr>
                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Código / asignatura</th>
                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Profesor / horario</th>
                    <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Cupo</th>
                    <th scope="col" class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($grupos as $grupo)
                        <tr>
                            <td class="px-5 py-4"><span class="block text-sm font-semibold text-slate-900">{{ $grupo->codigo }}</span><span class="mt-1 block text-sm text-slate-600">{{ $grupo->asignatura->nombre }}</span></td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $grupo->periodo->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700"><span class="block">{{ $grupo->profesor?->nombre ?? $grupo->docente ?? 'Pendiente de asignación' }}</span><span class="mt-1 block text-slate-500">{{ $grupo->horario }}</span></td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $grupo->cupo }}</td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-4">
                                <a href="{{ route('staff.grupos.edit', $grupo) }}" class="text-sm font-semibold text-indigo-700">Editar</a>
                                <form method="POST" action="{{ route('staff.grupos.destroy', $grupo) }}" onsubmit="return confirm('¿Eliminar este grupo?');">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-700">Eliminar</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Aún no hay grupos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($grupos->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $grupos->links() }}</div>@endif
    </div>
@endsection
