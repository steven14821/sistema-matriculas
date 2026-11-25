<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Configurar Authenticator | Saint Victoria Academy</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
        <section class="w-full max-w-xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-900/5 sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-700">Protección de la cuenta</p>
            <h1 class="mt-3 font-serif text-3xl font-semibold tracking-tight text-slate-950">Configura tu Authenticator</h1>

            @if ($user->two_factor_secret === null)
                <p class="mt-4 text-sm leading-6 text-slate-600">
                    Para continuar, agrega la verificación en dos pasos. Genera un código QR y escanéalo con Google Authenticator, Microsoft Authenticator o una app compatible.
                </p>

                <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-indigo-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/25">
                        Generar código QR
                    </button>
                </form>
            @elseif ($user->two_factor_confirmed_at === null)
                <p class="mt-4 text-sm leading-6 text-slate-600">
                    Escanea este código QR con tu app Authenticator y luego ingresa el código de seis dígitos para confirmar la configuración.
                </p>

                <div class="mt-6 flex justify-center rounded-xl border border-slate-200 bg-white p-4">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>

                <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-8 space-y-4">
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
                            required
                            autofocus
                            aria-invalid="{{ $errors->getBag('confirmTwoFactorAuthentication')->has('code') ? 'true' : 'false' }}"
                            @if ($errors->getBag('confirmTwoFactorAuthentication')->has('code')) aria-describedby="code-error" @endif
                            class="block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center text-lg tracking-[0.4em] text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15"
                        >
                        @error('code', 'confirmTwoFactorAuthentication')
                            <p id="code-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-indigo-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/25">
                        Confirmar y continuar
                    </button>
                </form>
            @else
                <p class="mt-4 text-sm leading-6 text-slate-600">
                    La verificación en dos pasos está activa. Guarda estos códigos de recuperación en un lugar seguro; cada código se puede usar una sola vez si pierdes acceso a tu app.
                </p>

                <ul class="mt-6 grid gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 font-mono text-sm sm:grid-cols-2">
                    @foreach ($user->recoveryCodes() as $recoveryCode)
                        <li>{{ $recoveryCode }}</li>
                    @endforeach
                </ul>

                <a href="{{ route('dashboard') }}" class="mt-8 flex w-full items-center justify-center rounded-xl bg-indigo-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/15 transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-500/25">
                    Continuar al sistema
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-4 hover:text-slate-900">
                    Cerrar sesión
                </button>
            </form>
        </section>
    </main>
</body>
</html>
