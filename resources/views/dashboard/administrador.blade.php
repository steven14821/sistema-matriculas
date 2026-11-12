@extends('layouts.app')

@section('title', 'Administración')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Administración académica</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Gestiona el acceso al sistema, la oferta institucional y los registros de matrícula.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300" aria-labelledby="admin-tasks-title">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 py-5 sm:flex-row sm:items-end">
            <h2 id="admin-tasks-title" class="text-2xl font-medium text-slate-950">Gestión del sistema</h2>
        </div>
        <ul class="divide-y divide-slate-200">
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Usuarios y permisos</h3>
                    <p class="mt-1 text-sm text-slate-600">Crea cuentas y administra sus roles de acceso.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Gestionar usuarios <span aria-hidden="true">→</span></a>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Oferta académica</h3>
                    <p class="mt-1 text-sm text-slate-600">Actualiza programas, asignaturas, periodos, grupos y profesores.</p>
                </div>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                    <a href="{{ route('admin.programas.index') }}" class="text-indigo-700 hover:text-indigo-900">Programas</a>
                    <a href="{{ route('admin.asignaturas.index') }}" class="text-indigo-700 hover:text-indigo-900">Asignaturas</a>
                    <a href="{{ route('admin.periodos.index') }}" class="text-indigo-700 hover:text-indigo-900">Periodos</a>
                    <a href="{{ route('admin.grupos.index') }}" class="text-indigo-700 hover:text-indigo-900">Grupos</a>
                    <a href="{{ route('admin.profesores.index') }}" class="text-indigo-700 hover:text-indigo-900">Profesores</a>
                </div>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Matrículas</h3>
                    <p class="mt-1 text-sm text-slate-600">Consulta registros y apoya la inscripción de estudiantes.</p>
                </div>
                <a href="{{ route('admin.matriculas.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Consultar matrículas <span aria-hidden="true">→</span></a>
            </li>
        </ul>
    </section>
@endsection
