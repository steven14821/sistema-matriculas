@extends('layouts.app')

@section('title', 'Panel del estudiante')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Tu espacio académico</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Panel del estudiante</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Consulta la oferta académica y gestiona tu matrícula desde un mismo lugar.
        </p>
    </div>

    <section class="mt-9 grid gap-5 md:grid-cols-2" aria-label="Herramientas del estudiante">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="m3 9 9-5 9 5-9 5-9-5ZM7 11.2v4.3c2.8 2.2 7.2 2.2 10 0v-4.3M21 9v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Oferta de grupos</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Explora asignaturas, horarios y cupos disponibles para el periodo académico.</p>
            <span class="mt-5 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Módulo en preparación</span>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-800" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Mi matrícula</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Selecciona grupos según su disponibilidad y revisa el estado de tu inscripción.</p>
            <span class="mt-5 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Módulo en preparación</span>
        </article>
    </section>
@endsection
