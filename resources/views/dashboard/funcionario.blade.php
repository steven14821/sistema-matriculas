@extends('layouts.app')

@section('title', 'Panel del funcionario')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-700">Espacio de trabajo</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Panel del funcionario</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Administra la oferta académica y apoya los procesos de inscripción y matrícula.
        </p>
    </div>

    <section class="mt-9 grid gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="Herramientas del funcionario">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-800" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Gestión de matrículas</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Asigna asignaturas y grupos a estudiantes durante el proceso de matrícula.</p>
            <a href="{{ route('staff.enrollments.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">Abrir matrículas <span class="ml-2" aria-hidden="true">→</span></a>
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
            <a href="{{ route('staff.enrollments.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">Consultar registros <span class="ml-2" aria-hidden="true">→</span></a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Carreras y asignaturas</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Crea carreras y vincula cada asignatura con su carrera correspondiente.</p>
            <div class="mt-5 flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('staff.programas.index') }}" class="text-indigo-700 hover:text-indigo-900">Carreras</a>
                <a href="{{ route('staff.asignaturas.index') }}" class="text-indigo-700 hover:text-indigo-900">Asignaturas</a>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-950">Periodos y grupos</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Configura fechas de matrícula y abre grupos con docente, horario y cupo.</p>
            <div class="mt-5 flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('staff.periodos.index') }}" class="text-indigo-700 hover:text-indigo-900">Periodos</a>
                <a href="{{ route('staff.grupos.index') }}" class="text-indigo-700 hover:text-indigo-900">Grupos</a>
            </div>
        </article>
    </section>
@endsection
