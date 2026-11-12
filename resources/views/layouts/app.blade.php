<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Panel') | Saint Victoria Academy</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-app min-h-screen bg-slate-50 text-slate-900 antialiased">
    @php
        $user = auth()->user();
        $navigation = $user->role->value === 'administrador'
            ? [
                ['Panel', 'dashboard', 'dashboard'],
                ['Usuarios', 'admin.users.index', 'admin.users.*'],
                ['Matrículas', 'admin.matriculas.index', 'admin.matriculas.*'],
                ['Programas', 'admin.programas.index', 'admin.programas.*'],
                ['Asignaturas', 'admin.asignaturas.index', 'admin.asignaturas.*'],
                ['Periodos', 'admin.periodos.index', 'admin.periodos.*'],
                ['Grupos', 'admin.grupos.index', 'admin.grupos.*'],
                ['Profesores', 'admin.profesores.index', 'admin.profesores.*'],
            ]
            : ($user->role->value === 'funcionario'
                ? [
                    ['Panel', 'dashboard', 'dashboard'],
                    ['Matrículas', 'admin.matriculas.index', 'admin.matriculas.*'],
                    ['Grupos', 'admin.grupos.index', 'admin.grupos.*'],
                    ['Profesores', 'admin.profesores.index', 'admin.profesores.*'],
                    ['Oferta académica', 'oferta.index', 'oferta.*'],
                ]
                : [
                    ['Panel', 'dashboard', 'dashboard'],
                    ['Oferta académica', 'oferta.index', 'oferta.*'],
                    ['Mi matrícula', 'matriculas.mine', 'matriculas.mine'],
                ]);
    @endphp

    <div class="academic-shell min-h-screen">
        <aside class="academic-sidebar">
            <a href="{{ route('dashboard') }}" class="academic-brand">
                <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="academic-brand__seal">
                <span class="min-w-0">
                    <span class="academic-brand__name">Saint Victoria</span>
                    <span class="academic-brand__sub">Academy · Matrículas</span>
                </span>
            </a>

            <div class="academic-sidebar__label">Espacio académico</div>
            <nav class="academic-nav" aria-label="Navegación principal">
                @foreach ($navigation as [$label, $routeName, $activeRoute])
                    <a href="{{ route($routeName) }}" @if (request()->routeIs($activeRoute)) aria-current="page" @endif>
                        <span class="academic-nav__mark" aria-hidden="true"></span>
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="academic-sidebar__foot">
                <span class="academic-sidebar__foot-rule" aria-hidden="true"></span>
                <span>Saint Victoria Academy</span>
                <span>Gestión académica</span>
            </div>
        </aside>

        <div class="academic-workspace">
            <header class="academic-topbar">
                <div class="academic-topbar__context">
                    <span class="academic-topbar__eyebrow">Sistema institucional</span>
                    <span class="academic-topbar__divider" aria-hidden="true"></span>
                    <span class="academic-topbar__current">@yield('title', 'Panel')</span>
                </div>
                <div class="academic-account">
                    <span class="academic-account__initial" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</span>
                    <span class="academic-account__identity">
                        <span class="academic-account__name">{{ $user->name }}</span>
                        <span class="academic-account__role">{{ ucfirst($user->role->value) }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="academic-account__logout" type="submit">Salir</button>
                    </form>
                </div>
            </header>

            <nav class="academic-mobile-nav" aria-label="Navegación principal">
                <a href="{{ route('dashboard') }}" class="academic-mobile-nav__brand">
                    <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="" class="academic-mobile-nav__seal">
                    <span>Saint Victoria</span>
                </a>
                @foreach ($navigation as [$label, $routeName, $activeRoute])
                    <a href="{{ route($routeName) }}" @if (request()->routeIs($activeRoute)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <main class="academic-main">
                @if (session('status'))
                    <div class="academic-notice" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="academic-notice academic-notice--error" role="alert">
                        <p class="font-semibold">Revisa la información ingresada.</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
