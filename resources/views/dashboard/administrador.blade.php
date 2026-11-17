@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Administración</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Panel de administración</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Crea usuarios, asigna roles y consulta la información académica y las matrículas de la institución.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Gestionar usuarios
        </a>
    </div>

    <section class="mt-9 grid gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="Herramientas de administración">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6m3-3h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Usuarios y roles</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Crea cuentas nuevas y asigna el rol de Administrador, Funcionario o Estudiante.</p>
            <a href="{{ route('admin.users.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                Abrir gestión de usuarios <span class="ml-2" aria-hidden="true">→</span>
            </a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Catálogo académico</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Consulta carreras, asignaturas, periodos, grupos y cupos sin modificarlos.</p>
            <a href="{{ route('admin.catalogo.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                Ver catálogo académico <span class="ml-2" aria-hidden="true">→</span>
            </a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6m3-3h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Estudiantes y matrículas</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Consulta cada estudiante, su carrera, las asignaturas inscritas y el historial de matrícula.</p>
            <a href="{{ route('admin.estudiantes.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                Ver estudiantes <span class="ml-2" aria-hidden="true">→</span>
            </a>
        </article>
    </section>
@endsection
