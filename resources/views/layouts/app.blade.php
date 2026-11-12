<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Sistema de Matrículas') | Saint Victoria Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white/90 shadow-sm shadow-slate-900/5 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="Saint Victoria Academy" class="size-12 rounded-xl object-cover shadow-sm ring-1 ring-slate-200">
                <span>
                    <span class="block font-serif text-base font-semibold tracking-wide text-slate-950">Saint Victoria Academy</span>
                    <span class="block text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Sistema de matrículas</span>
                </span>
            </a>

            @auth
                <nav aria-label="Navegación principal" class="flex flex-wrap items-center gap-3 sm:gap-5">
                    @can('manage-users')
                        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-indigo-700">
                            Usuarios
                        </a>
                    @endcan
                    <span class="hidden text-sm text-slate-600 sm:inline">{{ auth()->user()->name }}</span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ auth()->user()->role->label() }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">
                            Cerrar sesión
                        </button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
