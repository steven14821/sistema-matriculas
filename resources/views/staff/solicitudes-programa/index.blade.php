@extends('layouts.app')

@section('title', 'Solicitudes de programa')

@section('content')
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Solicitudes de programa</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Revisa el recibo de pago antes de asignar un programa y habilitar la oferta académica del estudiante.
        </p>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estudiante</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Programa solicitado</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Enviado</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        <th scope="col" class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Recibo y decisión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td class="px-5 py-4">
                                <span class="block text-sm font-semibold text-slate-900">{{ $solicitud->estudiante->usuario->name }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $solicitud->estudiante->usuario->email }}</span>
                                <span class="mt-1 block text-xs text-slate-500">Código {{ $solicitud->estudiante->codigo }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-slate-800">{{ $solicitud->programa->nombre }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $solicitud->created_at->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-amber-50 text-amber-800' => $solicitud->estado === 'pendiente',
                                    'bg-emerald-50 text-emerald-700' => $solicitud->estado === 'aprobada',
                                    'bg-red-50 text-red-700' => $solicitud->estado === 'rechazada',
                                ])>{{ ucfirst($solicitud->estado) }}</span>
                                @if ($solicitud->observacion)
                                    <p class="mt-2 max-w-xs text-xs leading-5 text-slate-600">{{ $solicitud->observacion }}</p>
                                @endif
                            </td>
                            <td class="min-w-64 px-5 py-4">
                                <a href="{{ route('staff.solicitudes_programa.comprobante', $solicitud) }}" class="text-sm font-semibold text-indigo-700 underline decoration-indigo-300 underline-offset-4 hover:text-indigo-900">
                                    Descargar recibo
                                </a>

                                @if ($solicitud->estado === 'pendiente')
                                    <div class="mt-3 flex flex-wrap items-start gap-3">
                                        <form method="POST" action="{{ route('staff.solicitudes_programa.update', $solicitud) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                                                Aprobar y asignar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.solicitudes_programa.update', $solicitud) }}" class="min-w-56 flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="rechazada">
                                            <label for="observacion-{{ $solicitud->id }}" class="sr-only">Motivo del rechazo para {{ $solicitud->estudiante->usuario->name }}</label>
                                            <textarea id="observacion-{{ $solicitud->id }}" name="observacion" rows="2" maxlength="1000" required placeholder="Indica qué debe corregir" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"></textarea>
                                            <button type="submit" class="mt-2 rounded-xl border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-800 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-500/15">
                                                Rechazar solicitud
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        Revisada {{ $solicitud->revisado_en?->format('d/m/Y') ?? '' }}
                                        @if ($solicitud->revisor)
                                            por {{ $solicitud->revisor->name }}
                                        @endif
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-sm font-semibold text-slate-900">Aún no hay solicitudes de programa</p>
                                <p class="mt-1 text-sm text-slate-500">Cuando un estudiante envíe su recibo, podrás revisarlo aquí.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($solicitudes->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $solicitudes->links() }}</div>
        @endif
    </div>
@endsection
