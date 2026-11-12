<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Saint Victoria Academy | Sistema de Matrículas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
        <header class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="Saint Victoria Academy" class="size-12 rounded-xl object-cover shadow-sm ring-1 ring-slate-200">
                <span>
                    <span class="block font-serif text-base font-semibold tracking-wide text-slate-950">Saint Victoria Academy</span>
                    <span class="block text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Sistema de matrículas</span>
                </span>
            </a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800">
                {{ auth()->check() ? 'Ir a mi panel' : 'Iniciar sesión' }}
            </a>
        </header>

        <section class="grid flex-1 items-center gap-12 py-12 sm:py-16 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="relative">
                <p class="mb-5 inline-flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.24em] text-indigo-700">
                    <span class="h-px w-8 bg-amber-700"></span>
                    Comunidad académica
                </p>
                <h1 class="max-w-3xl font-serif text-5xl font-medium leading-[1.08] tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
                    Una tradición de conocimiento. Tu futuro comienza aquí.
                </h1>
                <p class="mt-6 max-w-xl text-base leading-8 text-slate-600 sm:text-lg">
                    Consulta y gestiona tu vida académica en un espacio seguro, creado para acompañar cada etapa de tu formación.
                </p>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="mt-8 inline-flex items-center gap-3 rounded-xl bg-indigo-700 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:-translate-y-0.5 hover:bg-indigo-800">
                    {{ auth()->check() ? 'Continuar al panel' : 'Acceder al sistema' }}
                    <span aria-hidden="true">↗</span>
                </a>
                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 border-t border-slate-200 pt-6 text-xs font-medium uppercase tracking-[0.16em] text-slate-500">
                    <span class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-amber-700"></span>Excelencia académica</span>
                    <span class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-amber-700"></span>Comunidad y tradición</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                <article class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 sm:col-span-3 lg:col-span-1">
                    <div class="absolute -right-14 -top-20 size-56 rounded-full bg-amber-50 blur-3xl" aria-hidden="true"></div>
                    <div class="relative flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left lg:flex-col lg:text-center">
                        <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="size-40 rounded-2xl object-cover shadow-md ring-1 ring-slate-200 sm:size-32 lg:size-48">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">Saint Victoria Academy</p>
                            <h2 class="mt-2 font-serif text-2xl font-semibold text-slate-950">Tradición, conocimiento y victoria</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Una comunidad que inspira a aprender y alcanzar nuevas metas.</p>
                        </div>
                    </div>
                </article>
                @foreach ([
                    ['01', 'Administración', 'Usuarios y configuración del sistema'],
                    ['02', 'Funcionario', 'Apoyo a la gestión de matrículas'],
                    ['03', 'Estudiante', 'Consulta y autogestión académica'],
                ] as [$number, $role, $description])
                    <article class="rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-start gap-4">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xs font-bold text-indigo-700">{{ $number }}</span>
                            <div>
                                <h2 class="font-semibold text-slate-950">{{ $role }}</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $description }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <footer class="border-t border-slate-200 py-5 text-sm text-slate-500">
            © {{ date('Y') }} Saint Victoria Academy · Sistema de Matrículas
        </footer>
    </main>
</body>
</html>
