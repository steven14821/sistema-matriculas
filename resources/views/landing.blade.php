<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Sistema de Matrículas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
        <header class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-950 text-cyan-200" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none">
                        <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M7 11.2v4.3c2.8 2.2 7.2 2.2 10 0v-4.3M21 9v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold">Sistema de Matrículas</span>
                    <span class="block text-xs text-slate-500">Gestión académica</span>
                </span>
            </a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="rounded-xl bg-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
                {{ auth()->check() ? 'Ir a mi panel' : 'Iniciar sesión' }}
            </a>
        </header>

        <section class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <p class="mb-5 text-xs font-semibold uppercase tracking-[0.24em] text-indigo-700">Comunidad académica</p>
                <h1 class="max-w-3xl text-4xl font-semibold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                    Tu vida académica, en un solo lugar.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                    Consulta y gestiona la información de matrícula con un acceso seguro diseñado para cada perfil institucional.
                </p>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-indigo-700 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800">
                    {{ auth()->check() ? 'Continuar al panel' : 'Acceder al sistema' }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                @foreach ([
                    ['01', 'Administración', 'Usuarios y configuración del sistema'],
                    ['02', 'Funcionario', 'Apoyo a la gestión de matrículas'],
                    ['03', 'Estudiante', 'Consulta y autogestión académica'],
                ] as [$number, $role, $description])
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
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
            © {{ date('Y') }} Sistema de Matrículas
        </footer>
    </main>
</body>
</html>
