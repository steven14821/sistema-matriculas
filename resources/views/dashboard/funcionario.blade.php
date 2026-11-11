@extends('layouts.app')

@section('title', 'Panel del funcionario')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Espacio de trabajo</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Panel del funcionario</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Desde aquí podrás apoyar los procesos de inscripción y consultar las matrículas de la institución.
        </p>
    </div>

    <section class="mt-9 grid gap-5 md:grid-cols-2" aria-label="Herramientas del funcionario">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-800" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Gestión de matrículas</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Asigna asignaturas y grupos a estudiantes durante el proceso de matrícula.</p>
            <span class="mt-5 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Módulo en preparación</span>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/>
                    <path d="m20 20-4-4M11 8v6M8 11h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Consulta y búsqueda</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Filtra los registros de matrícula por estudiante o periodo académico.</p>
            <span class="mt-5 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Módulo en preparación</span>
        </article>
    </section>
@endsection
