@extends('layouts.app')

@section('title', 'Periodos académicos')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Gestión académica</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Periodos académicos</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">Controla fechas y disponibilidad de matrícula.</p>
        </div>
        <a href="{{ route('staff.periodos.create') }}" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-800">Crear periodo</a>
    </div>
    @error('periodo')<div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>@enderror
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50"><tr>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periodo</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Fechas</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Matrícula</th>
                    <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($periodos as $periodo)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $periodo->nombre }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $periodo->fecha_inicio->format('d/m/Y') }} – {{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-sm">{{ $periodo->abierto ? 'Abierta' : 'Cerrada' }}</td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-4">
                                <a href="{{ route('staff.periodos.edit', $periodo) }}" class="text-sm font-semibold text-indigo-700">Editar</a>
                                <form method="POST" action="{{ route('staff.periodos.destroy', $periodo) }}" onsubmit="return confirm('¿Eliminar este periodo?');">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-700">Eliminar</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">Aún no hay periodos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($periodos->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $periodos->links() }}</div>@endif
    </div>
@endsection
