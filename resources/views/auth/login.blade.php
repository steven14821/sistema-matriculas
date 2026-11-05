<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Iniciar sesión | Sistema de Matrículas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="min-h-screen md:grid md:grid-cols-2">
        <section class="relative hidden min-h-screen overflow-hidden bg-linear-to-br from-slate-950 via-slate-900 to-pink-950 px-12 py-10 text-white md:flex md:flex-col md:justify-between lg:px-16">
            <div class="absolute -right-32 -top-32 size-96 rounded-full bg-pink-500/15 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-40 -left-24 size-96 rounded-full bg-fuchsia-400/10 blur-3xl" aria-hidden="true"></div>

            <a href="{{ url('/') }}" class="relative flex w-fit items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/15">
                    <svg class="size-6 text-pink-200" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M7 11.2v4.3c2.8 2.2 7.2 2.2 10 0v-4.3M21 9v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold tracking-wide">Sistema de Matrículas</span>
                    <span class="block text-xs text-slate-300">Gestión académica</span>
                </span>
            </a>

            <div class="relative max-w-lg py-16">
                <p class="mb-5 text-xs font-semibold uppercase tracking-[0.24em] text-pink-200">Tu espacio académico</p>
                <h1 class="text-4xl font-semibold leading-tight tracking-tight lg:text-5xl">
                    Un nuevo periodo, nuevas oportunidades.
                </h1>
                <p class="mt-6 max-w-md text-base leading-7 text-slate-300">
                    Ingresa al sistema para consultar y gestionar tu información académica de forma segura.
                </p>

                <div class="mt-12 flex items-center gap-4 border-t border-white/10 pt-6">
                    <span class="flex size-10 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 5 6v5c0 4.5 2.9 7.7 7 10 4.1-2.3 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <p class="text-sm text-slate-300">Acceso protegido para tu comunidad académica</p>
                </div>
            </div>

            <p class="relative text-xs text-slate-400">© {{ date('Y') }} Sistema de Matrículas</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <a href="{{ url('/') }}" class="mb-10 flex w-fit items-center gap-3 md:hidden">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-pink-950 text-pink-200">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m3 9 9-5 9 5-9 5-9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="M7 11.2v4.3c2.8 2.2 7.2 2.2 10 0v-4.3M21 9v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold">Sistema de Matrículas</span>
                        <span class="block text-xs text-slate-500">Gestión académica</span>
                    </span>
                </a>

                <div class="mb-8">
                    <p class="text-sm font-semibold text-pink-700">Bienvenido de nuevo</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Iniciar sesión</h2>
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
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-4 focus:ring-pink-500/15"
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
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-4 focus:ring-pink-500/15"
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
                            class="size-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                        >
                        Recordarme en este dispositivo
                    </label>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-pink-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus:ring-4 focus:ring-pink-500/25"
                    >
                        Ingresar
                    </button>
                </form>

                <p class="mt-8 border-t border-slate-200 pt-6 text-center text-xs leading-5 text-slate-500">
                    El acceso está reservado para usuarios autorizados de la institución.
                </p>
            </div>
        </section>
    </main>
</body>
</html>
