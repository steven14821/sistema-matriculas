@extends('layouts.app')

@section('title', 'Gestión académica')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Gestión académica</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Administra carreras, asignaturas, grupos y matrículas.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300" aria-labelledby="staff-tasks-title">
        <div class="border-b border-slate-200 py-5">
            <h2 id="staff-tasks-title" class="text-2xl font-medium text-slate-950">Tareas del funcionario</h2>
        </div>
        <ul class="divide-y divide-slate-200">
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Carreras y asignaturas</h3>
                    <p class="mt-1 text-sm text-slate-600">Crea carreras y asocia cada materia con su carrera.</p>
                </div>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                    <a href="{{ route('staff.programas.index') }}" class="text-indigo-700 hover:text-indigo-900">Carreras</a>
                    <a href="{{ route('staff.asignaturas.index') }}" class="text-indigo-700 hover:text-indigo-900">Asignaturas</a>
                </div>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Periodos, grupos y profesores</h3>
                    <p class="mt-1 text-sm text-slate-600">Organiza la oferta académica y asigna docentes a los grupos.</p>
                </div>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                    <a href="{{ route('staff.periodos.index') }}" class="text-indigo-700 hover:text-indigo-900">Periodos</a>
                    <a href="{{ route('staff.grupos.index') }}" class="text-indigo-700 hover:text-indigo-900">Grupos</a>
                    <a href="{{ route('staff.profesores.index') }}" class="text-indigo-700 hover:text-indigo-900">Profesores</a>
                </div>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Matrículas</h3>
                    <p class="mt-1 text-sm text-slate-600">Busca matrículas y apoya las inscripciones de estudiantes.</p>
                </div>
                <a href="{{ route('staff.enrollments.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Abrir matrículas <span aria-hidden="true">→</span></a>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Oferta académica</h3>
                    <p class="mt-1 text-sm text-slate-600">Consulta los grupos disponibles para el periodo abierto.</p>
                </div>
                <a href="{{ route('oferta.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Ver oferta <span aria-hidden="true">→</span></a>
            </li>
        </ul>
    </section>
@endsection
