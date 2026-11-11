@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-700">Administración</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Panel de administración</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                Administra las cuentas y los perfiles que tienen acceso al sistema de matrículas.
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
            Gestionar usuarios
        </a>
    </div>

    <section class="mt-9 grid gap-5 md:grid-cols-2" aria-label="Herramientas de administración">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700" aria-hidden="true">
                <svg class="size-5" viewBox="0 0 24 24" fill="none">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6m3-3h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Usuarios y roles</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Crea cuentas y asigna los perfiles de Administrador, Funcionario o Estudiante.</p>
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
            <h2 class="mt-5 text-lg font-semibold text-slate-950">Gestión académica</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Los módulos de programas, asignaturas, periodos y grupos estarán disponibles aquí.</p>
            <span class="mt-5 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">En preparación</span>
        </article>
    </section>
@endsection
