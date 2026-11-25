<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Verificación de dos pasos | Saint Victoria Academy</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
        <section class="w-full max-w-md border border-slate-200 bg-white p-6 shadow-lg shadow-slate-900/5 sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-700">Verificación de seguridad</p>
            <h1 class="mt-3 font-serif text-3xl font-semibold tracking-tight text-slate-950">Confirma tu identidad</h1>
            <p class="mt-4 text-sm leading-6 text-slate-600">
                Abre tu app Authenticator e ingresa el código de seis dígitos para completar el inicio de sesión.
            </p>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-8 space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-slate-700">Código de Authenticator</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autofocus
                        aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}"
                        @if ($errors->has('code')) aria-describedby="code-error" @endif
                        class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center text-lg tracking-[0.4em] text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"
                    >
                    @error('code')
                        <p id="code-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-indigo-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/25">
                    Verificar e ingresar
                </button>
            </form>

            <div class="my-8 border-t border-slate-200"></div>

            <p class="text-sm leading-6 text-slate-600">
                Si no tienes acceso a la app, usa uno de tus códigos de recuperación.
            </p>
            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="recovery_code" class="mb-2 block text-sm font-medium text-slate-700">Código de recuperación</label>
                    <input
                        id="recovery_code"
                        name="recovery_code"
                        type="text"
                        autocomplete="one-time-code"
                        aria-invalid="{{ $errors->has('recovery_code') ? 'true' : 'false' }}"
                        @if ($errors->has('recovery_code')) aria-describedby="recovery-code-error" @endif
                        class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"
                    >
                    @error('recovery_code')
                        <p id="recovery-code-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    Usar código de recuperación
                </button>
            </form>
        </section>
    </main>
</body>
</html>
