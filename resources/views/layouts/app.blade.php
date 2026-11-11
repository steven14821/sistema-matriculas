<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Sistema de Matrículas') | Sistema de Matrículas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-950 text-cyan-200" aria-hidden="true">
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
