<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Saint Victoria Academy | Sistema de Matrículas</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-[1440px] flex-col px-5 sm:px-8 lg:px-14">
        <header class="flex items-center justify-between gap-5 border-b border-slate-200 py-5">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="size-12 rounded-full border border-amber-700/40 bg-white object-cover">
                <span>
                    <span class="block font-serif text-lg font-semibold text-slate-950">Saint Victoria Academy</span>
                    <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Sistema de matrículas</span>
                </span>
            </a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="border border-indigo-700 px-4 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-700 hover:text-white">
                {{ auth()->check() ? 'Ir a mi panel' : 'Iniciar sesión' }}
            </a>
        </header>

        <section class="grid flex-1 items-center gap-12 py-14 md:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:gap-20">
            <div class="max-w-3xl">
                <h1 class="max-w-3xl font-serif text-5xl font-medium leading-[1.08] tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
                    Tu recorrido académico, en orden.
                </h1>
                <p class="mt-7 max-w-xl text-base leading-8 text-slate-600 sm:text-lg">
                    Consulta la oferta, realiza tu matrícula y encuentra la información de tu periodo académico en un solo lugar.
                </p>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="mt-9 inline-flex items-center gap-4 bg-indigo-700 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
                    {{ auth()->check() ? 'Continuar al panel' : 'Acceder al sistema' }}
                    <span aria-hidden="true">→</span>
                </a>

                <div class="mt-14 grid max-w-xl grid-cols-3 border-y border-slate-200 py-5">
                    @foreach ([['01', 'Oferta'], ['02', 'Matrícula'], ['03', 'Seguimiento']] as [$number, $label])
                        <div class="flex items-center gap-2.5 {{ $loop->index > 0 ? 'border-l border-slate-200 pl-4 sm:pl-6' : '' }}">
                            <span class="font-serif text-xs text-amber-800">{{ $number }}</span>
                            <span class="text-xs font-medium text-slate-700 sm:text-sm">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="relative mx-auto w-full max-w-[460px] lg:mr-0">
                <div class="absolute -inset-3 border border-amber-700/50" aria-hidden="true"></div>
                <div class="relative flex min-h-[430px] flex-col items-center justify-center overflow-hidden bg-indigo-800 px-8 py-12 text-center text-white sm:min-h-[500px]">
                    <div class="absolute inset-3 border border-amber-200/30" aria-hidden="true"></div>
                    <div class="absolute left-1/2 top-0 h-20 w-px -translate-x-1/2 bg-amber-200/50" aria-hidden="true"></div>
                    <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="Emblema de Saint Victoria Academy" class="relative size-48 rounded-full border border-amber-200/70 bg-slate-50 object-cover shadow-[0_0_0_10px_rgba(234,211,154,0.08)] sm:size-56">
                    <p class="relative mt-8 font-serif text-2xl tracking-wide sm:text-3xl">Saint Victoria Academy</p>
                    <p class="relative mt-2 text-[10px] font-semibold uppercase tracking-[0.22em] text-amber-200">Sistema institucional de matrículas</p>
                    <div class="absolute bottom-0 left-1/2 h-12 w-px -translate-x-1/2 bg-amber-200/50" aria-hidden="true"></div>
                </div>
            </aside>
        </section>

        <footer class="flex flex-col gap-2 border-t border-slate-200 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>© {{ date('Y') }} Saint Victoria Academy</span>
            <span>Oferta académica · Matrículas · Seguimiento</span>
        </footer>
    </main>
</body>
</html>
