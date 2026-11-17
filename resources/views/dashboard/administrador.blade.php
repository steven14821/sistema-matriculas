@extends('layouts.app')

@section('title', 'Administración')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Administración académica</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Gestiona cuentas y consulta la información académica y las matrículas de la institución.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300" aria-labelledby="admin-tasks-title">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 py-5 sm:flex-row sm:items-end">
            <h2 id="admin-tasks-title" class="text-2xl font-medium text-slate-950">Gestión del sistema</h2>
        </div>
        <ul class="divide-y divide-slate-200">
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Usuarios y roles</h3>
                    <p class="mt-1 text-sm text-slate-600">Crea usuarios y asigna o actualiza sus roles de acceso.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Gestionar usuarios <span aria-hidden="true">→</span></a>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Catálogo académico</h3>
                    <p class="mt-1 text-sm text-slate-600">Consulta carreras, materias, periodos y grupos en modo de solo lectura.</p>
                </div>
                <a href="{{ route('admin.catalogo.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Ver catálogo <span aria-hidden="true">→</span></a>
            </li>
            <li class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Estudiantes y matrículas</h3>
                    <p class="mt-1 text-sm text-slate-600">Revisa la carrera y el historial de asignaturas de cada estudiante.</p>
                </div>
                <a href="{{ route('admin.estudiantes.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Consultar estudiantes <span aria-hidden="true">→</span></a>
            </li>
        </ul>
    </section>
@endsection
