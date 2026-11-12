@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <header class="max-w-3xl">
        <h1 class="text-4xl font-medium text-slate-950 sm:text-5xl">Tu próximo periodo empieza aquí.</h1>
        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
            Bienvenido, {{ auth()->user()->name }}. Sigue estos pasos para revisar los grupos disponibles y mantener tu información académica al día.
        </p>
    </header>

    <section class="mt-12 border-y border-slate-300 bg-white/70 px-5 py-7 sm:px-8 sm:py-9" aria-labelledby="enrollment-route-title">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 sm:flex-row sm:items-end">
            <div>
                <h2 id="enrollment-route-title" class="text-2xl font-medium text-slate-950">Ruta de matrícula</h2>
                <p class="mt-2 text-sm text-slate-600">Una guía breve para completar tu inscripción.</p>
            </div>
        </div>

        <ol class="grid gap-0 pt-3 md:grid-cols-3">
            <li class="relative border-b border-slate-200 py-6 md:border-b-0 md:pr-7">
                <span class="font-serif text-sm text-amber-800">01</span>
                <h3 class="mt-3 text-lg font-semibold text-slate-950">Explora la oferta</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-600">Consulta asignaturas, profesores y cupos abiertos.</p>
                <a href="{{ route('oferta.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                    Ver grupos disponibles <span aria-hidden="true">→</span>
                </a>
            </li>
            <li class="relative border-b border-slate-200 py-6 md:border-b-0 md:border-l md:border-slate-200 md:px-7">
                <span class="font-serif text-sm text-amber-800">02</span>
                <h3 class="mt-3 text-lg font-semibold text-slate-950">Elige tus grupos</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-600">Inscríbete en los grupos que tengan disponibilidad.</p>
                <a href="{{ route('oferta.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                    Revisar disponibilidad <span aria-hidden="true">→</span>
                </a>
            </li>
            <li class="py-6 md:border-l md:border-slate-200 md:pl-7">
                <span class="font-serif text-sm text-amber-800">03</span>
                <h3 class="mt-3 text-lg font-semibold text-slate-950">Confirma tu matrícula</h3>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-600">Consulta los grupos en los que quedaste inscrito.</p>
                <a href="{{ route('matriculas.mine') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                    Ver mi matrícula <span aria-hidden="true">→</span>
                </a>
            </li>
        </ol>
    </section>

    <section class="mt-9 flex flex-col justify-between gap-5 border-t-2 border-amber-700 bg-amber-50/70 px-5 py-5 sm:flex-row sm:items-center sm:px-7" aria-label="Acceso a la oferta académica">
        <div>
            <h2 class="text-xl font-medium text-slate-950">¿Listo para comenzar?</h2>
            <p class="mt-1 text-sm text-slate-600">Revisa la oferta académica disponible para matrícula.</p>
        </div>
        <a href="{{ route('oferta.index') }}" class="inline-flex w-fit items-center gap-3 bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">
            Consultar oferta <span aria-hidden="true">→</span>
        </a>
    </section>
@endsection
