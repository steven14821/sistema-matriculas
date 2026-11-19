@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Tu próximo periodo empieza aquí.</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Consulta los grupos de tu programa o solicita uno desde Mi matrícula si aún no tienes asignación.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300 bg-white/70 px-5 py-7 sm:px-8 sm:py-9" aria-labelledby="enrollment-route-title">
        <div class="border-b border-slate-200 pb-5">
            <h2 id="enrollment-route-title" class="text-2xl font-medium text-slate-950">Ruta de matrícula</h2>
            <p class="mt-2 text-sm text-slate-600">Consulta la oferta y mantén a la vista tu inscripción actual.</p>
        </div>

        <ol class="grid gap-0 pt-3 md:grid-cols-2">
            <li class="border-b border-slate-200 py-6 md:border-b-0 md:pr-7">
                <span class="font-serif text-sm text-amber-800">01</span>
                <h3 class="mt-3 text-lg font-semibold text-slate-950">Solicita tu programa o explora la oferta</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-600">Si no tienes programa asignado, elige uno y adjunta el recibo para que un funcionario lo revise.</p>
                <a href="{{ route('matriculas.mine') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Abrir mi matrícula <span aria-hidden="true">→</span></a>
            </li>
            <li class="py-6 md:border-l md:border-slate-200 md:pl-7">
                <span class="font-serif text-sm text-amber-800">02</span>
                <h3 class="mt-3 text-lg font-semibold text-slate-950">Consulta tu matrícula</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-600">Revisa las asignaturas en las que estás inscrito.</p>
                <a href="{{ route('matriculas.mine') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Ver mi matrícula <span aria-hidden="true">→</span></a>
            </li>
        </ol>
    </section>
@endsection
