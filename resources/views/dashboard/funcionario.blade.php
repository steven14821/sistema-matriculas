@extends('layouts.app')

@section('title', 'Gestión académica')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Gestión académica</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Administra las matrículas y mantén organizada la asignación de profesores a los grupos.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300" aria-labelledby="staff-tasks-title">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 py-5 sm:flex-row sm:items-end">
            <h2 id="staff-tasks-title" class="text-2xl font-medium text-slate-950">Tareas del funcionario</h2>
        </div>
        <ul class="divide-y divide-slate-200">
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Matrículas</h3>
                    <p class="mt-1 text-sm text-slate-600">Busca registros por estudiante, periodo o estado.</p>
                </div>
                <a href="{{ route('admin.matriculas.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Abrir matrículas <span aria-hidden="true">→</span></a>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Asignación de profesores</h3>
                    <p class="mt-1 text-sm text-slate-600">Mantén el catálogo y asigna docentes a los grupos.</p>
                </div>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                    <a href="{{ route('admin.grupos.index') }}" class="text-indigo-700 hover:text-indigo-900">Administrar grupos</a>
                    <a href="{{ route('admin.profesores.index') }}" class="text-indigo-700 hover:text-indigo-900">Ver profesores</a>
                </div>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Oferta académica</h3>
                    <p class="mt-1 text-sm text-slate-600">Consulta los grupos disponibles del periodo abierto.</p>
                </div>
                <a href="{{ route('oferta.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Ver oferta <span aria-hidden="true">→</span></a>
            </li>
        </ul>
    </section>
@endsection
