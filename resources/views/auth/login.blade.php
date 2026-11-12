<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Iniciar sesión | Saint Victoria Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="min-h-screen md:grid md:grid-cols-2">
        <section class="relative hidden min-h-screen overflow-hidden bg-linear-to-br from-slate-950 via-indigo-950 to-indigo-800 px-12 py-10 text-white md:flex md:flex-col md:justify-between lg:px-16">
            <div class="absolute -right-32 -top-32 size-96 rounded-full bg-amber-400/15 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-40 -left-24 size-96 rounded-full bg-indigo-500/20 blur-3xl" aria-hidden="true"></div>

            <a href="{{ url('/') }}" class="relative flex w-fit items-center gap-3">
                <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="Saint Victoria Academy" class="size-12 rounded-xl object-cover shadow ring-1 ring-white/20">
                <span>
                    <span class="block font-serif text-base font-semibold tracking-wide">Saint Victoria Academy</span>
                    <span class="block text-xs uppercase tracking-[0.16em] text-slate-300">Sistema de matrículas</span>
                </span>
            </a>

            <div class="relative max-w-lg py-16">
                <p class="mb-5 flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.24em] text-amber-200">
                    <span class="h-px w-8 bg-amber-300"></span>
                    Tu espacio académico
                </p>
                <h1 class="font-serif text-5xl font-medium leading-tight tracking-tight lg:text-6xl">
                    Un nuevo periodo, nuevas oportunidades.
                </h1>
                <p class="mt-6 max-w-md text-base leading-7 text-slate-300">
                    Ingresa a tu comunidad académica y gestiona tu información de forma segura.
                </p>

                <div class="mt-12 flex items-center gap-4 border-t border-white/10 pt-6">
                    <span class="flex size-10 items-center justify-center rounded-full bg-amber-300/10 text-amber-200">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 5 6v5c0 4.5 2.9 7.7 7 10 4.1-2.3 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <p class="text-sm text-slate-300">Acceso protegido para tu comunidad académica</p>
                </div>
            </div>

            <p class="relative text-xs text-slate-400">© {{ date('Y') }} Saint Victoria Academy</p>
        </section>

        <section class="flex min-h-screen items-center justify-center bg-slate-50 px-6 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <a href="{{ url('/') }}" class="mb-10 flex w-fit items-center gap-3 md:hidden">
                    <img src="{{ asset('images/saint-victoria-academy-logo.png') }}" alt="Saint Victoria Academy" class="size-12 rounded-xl object-cover shadow-sm ring-1 ring-slate-200">
                    <span>
                        <span class="block font-serif text-base font-semibold">Saint Victoria Academy</span>
                        <span class="block text-xs uppercase tracking-[0.16em] text-slate-500">Sistema de matrículas</span>
                    </span>
                </a>

                <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-8">
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-indigo-700">Bienvenido de nuevo</p>
                        <h2 class="mt-2 font-serif text-4xl font-semibold tracking-tight text-slate-950">Iniciar sesión</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Ingresa tus credenciales institucionales para continuar.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Correo electrónico</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                inputmode="email"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                @if ($errors->has('email')) aria-describedby="email-error" @endif
                                class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"
                                placeholder="nombre@institucion.edu.co"
                            >
                            @error('email')
                                <p id="email-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Contraseña</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                @if ($errors->has('password')) aria-describedby="password-error" @endif
                                class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"
                                placeholder="Ingresa tu contraseña"
                            >
                            @error('password')
                                <p id="password-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="remember" class="flex w-fit cursor-pointer items-center gap-3 text-sm text-slate-600">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                value="1"
                                class="size-4 rounded border-slate-300 text-indigo-700 focus:ring-indigo-500"
                            >
                            Recordarme en este dispositivo
                        </label>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl bg-indigo-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/25"
                        >
                            Ingresar
                        </button>
                    </form>

                    <p class="mt-8 border-t border-slate-200 pt-6 text-center text-xs leading-5 text-slate-500">
                        El acceso está reservado para usuarios autorizados de la institución.
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
